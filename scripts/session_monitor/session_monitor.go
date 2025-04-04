package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"os/signal"
	"strconv"
	"syscall"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

const (
	envDBHost           = "DB_HOST"
	envDBUsername       = "DB_USER"
	envDBPassword       = "DB_PASS"
	envDBName           = "DB_NAME"
	envTimeout          = "SESSION_TIMEOUT"
	envInactiveDays     = "INACTIVE_DAYS"
	defaultDBHost       = "127.0.0.1"
	defaultTimeout      = "1800"
	defaultInactiveDays = 30
	checkInterval       = 60      // Check every 60 seconds
	version             = "1.0.1" // Version of the session monitoring daemon
)

// getEnv retrieves environment variables with a default fallback
func getEnv(key, fallback string) string {
	if value, exists := os.LookupEnv(key); exists {
		return value
	}
	return fallback
}

// error_log logs errors in the required format
func error_log(message string) {
	log.Println("PV error:", message)
}

// cleanupExpiredSessions deletes expired sessions
func cleanupExpiredSessions(db *sql.DB, sessionTimeout int64) {
	currentTime := time.Now().Unix()

	query := "SELECT owner_id FROM session_info WHERE UNIX_TIMESTAMP(last_updated) < ?"
	rows, err := db.Query(query, currentTime-sessionTimeout)
	if err != nil {
		error_log(fmt.Sprintf("Error querying the database: %v", err))
		return
	}
	defer rows.Close()

	var expiredUsers []string
	for rows.Next() {
		var ownerID string
		if err := rows.Scan(&ownerID); err != nil {
			error_log(fmt.Sprintf("Error scanning row: %v", err))
			continue
		}
		expiredUsers = append(expiredUsers, ownerID)
	}

	if len(expiredUsers) == 0 {
		//	log.Println("No expired sessions found.")
		return
	}

	deleteQuery := "DELETE FROM session_info WHERE owner_id = ?"
	for _, ownerID := range expiredUsers {
		_, err := db.Exec(deleteQuery, ownerID)
		if err != nil {
			error_log(fmt.Sprintf("Error deleting session for owner_id %s: %v", ownerID, err))
		} else {
			log.Printf("Deleted expired session for owner_id: %s", ownerID)
		}
	}
}

// cleanupUnverifiedUsers deletes users who haven't verified their account after 2 days
func cleanupUnverifiedUsers(db *sql.DB) {
	twoDaysAgo := time.Now().Add(-48 * time.Hour).Unix()

	query := "SELECT id FROM users WHERE isVerified = 0 AND UNIX_TIMESTAMP(created_at) < ?"
	rows, err := db.Query(query, twoDaysAgo)
	if err != nil {
		error_log(fmt.Sprintf("Error querying unverified users: %v", err))
		return
	}
	defer rows.Close()

	var unverifiedUsers []string
	for rows.Next() {
		var userID string
		if err := rows.Scan(&userID); err != nil {
			error_log(fmt.Sprintf("Error scanning row: %v", err))
			continue
		}
		unverifiedUsers = append(unverifiedUsers, userID)
	}

	if len(unverifiedUsers) == 0 {
		//log.Println("No unverified users found.")
		return
	}

	deleteQuery := "DELETE FROM users WHERE id = ?"
	auditQuery := "INSERT INTO audit_log (email, ip, browser, timestamp, action, result) VALUES (?, ?, ?, ?, ?, ?)"
	for _, userID := range unverifiedUsers {
		_, err := db.Exec(deleteQuery, userID)
		if err != nil {
			error_log(fmt.Sprintf("Error deleting unverified user %s: %v", userID, err))
			_, auditErr := db.Exec(auditQuery, userID, "-", "-", time.Now(), "delete_unverified_user", fmt.Sprintf("failed: %v", err))
			if auditErr != nil {
				error_log(fmt.Sprintf("Error logging audit for user %s: %v", userID, auditErr))
			}
		} else {
			log.Printf("Deleted unverified user: %s", userID)
			_, auditErr := db.Exec(auditQuery, userID, "", "", time.Now(), "delete_unverified_user", "success")
			if auditErr != nil {
				error_log(fmt.Sprintf("Error logging audit for user %s: %v", userID, auditErr))
			}
		}
	}
}

// cleanupInactiveUsers deletes users who haven't logged in for the past N days
func cleanupInactiveUsers(db *sql.DB) {
	inactiveDaysStr := getEnv(envInactiveDays, strconv.Itoa(defaultInactiveDays))
	inactiveDays, err := strconv.Atoi(inactiveDaysStr)
	if err != nil {
		error_log(fmt.Sprintf("Invalid INACTIVE_DAYS value, using default: %d", defaultInactiveDays))
		inactiveDays = defaultInactiveDays
	}

	thirtyDaysAgo := time.Now().Add(-time.Duration(inactiveDays) * 24 * time.Hour).Unix()

	query := "SELECT id FROM users WHERE last_login < FROM_UNIXTIME(?) AND role = 2"
	rows, err := db.Query(query, thirtyDaysAgo)
	if err != nil {
		error_log(fmt.Sprintf("Error querying inactive users: %v", err))
		return
	}
	defer rows.Close()

	var inactiveUsers []string
	for rows.Next() {
		var userID string
		if err := rows.Scan(&userID); err != nil {
			error_log(fmt.Sprintf("Error scanning row: %v", err))
			continue
		}
		inactiveUsers = append(inactiveUsers, userID)
	}

	if len(inactiveUsers) == 0 {
		return
	}

	deleteQuery := "DELETE FROM users WHERE id = ?"
	auditQuery := "INSERT INTO audit_log (email, ip, browser, timestamp, action, result) VALUES (?, ?, ?, ?, ?, ?)"
	tables := []string{
		"batchIDHistory", "bottles", "cart", "customers", "documents",
		"formulaCategories", "formulas", "formulasMetaData", "formulasRevisions", "formulasTags",
		"formula_history", "IFRALibrary", "ingCategory", "ingredients", "ingredient_compounds",
		"ingredient_safety_data", "ingReplacements", "ingSafetyInfo", "ingSuppliers", "inventory_accessories",
		"inventory_compounds", "makeFormula", "perfumeTypes", "sds_data", "suppliers", "synonyms",
		"templates", "user_prefs", "user_settings", "branding", "orders", "order_items",
	}

	for _, userID := range inactiveUsers {
		// Delete from users table
		_, err := db.Exec(deleteQuery, userID)
		if err != nil {
			error_log(fmt.Sprintf("Error deleting inactive user %s: %v", userID, err))
			_, auditErr := db.Exec(auditQuery, userID, "-", "-", time.Now(), "delete_inactive_user", fmt.Sprintf("failed: %v", err))
			if auditErr != nil {
				error_log(fmt.Sprintf("Error logging audit for user %s: %v", userID, auditErr))
			}
		} else {
			log.Printf("Deleted inactive user: %s", userID)
			_, auditErr := db.Exec(auditQuery, userID, "", "", time.Now(), "delete_inactive_user", "success")
			if auditErr != nil {
				error_log(fmt.Sprintf("Error logging audit for user %s: %v", userID, auditErr))
			}

			// Delete from other tables
			for _, table := range tables {
				deleteTableQuery := fmt.Sprintf("DELETE FROM %s WHERE owner_id = ?", table)
				_, err := db.Exec(deleteTableQuery, userID)
				if err != nil {
					error_log(fmt.Sprintf("Error deleting from table %s for user %s: %v", table, userID, err))
				} else {
					log.Printf("Deleted records from table %s for user %s", table, userID)
				}
			}
		}
	}
}

func main() {
	dbhost := getEnv(envDBHost, defaultDBHost)
	dbuser := os.Getenv(envDBUsername)
	dbpass := os.Getenv(envDBPassword)
	dbname := os.Getenv(envDBName)
	sessionTimeoutStr := getEnv(envTimeout, defaultTimeout)
	inactiveDaysStr := getEnv(envInactiveDays, strconv.Itoa(defaultInactiveDays))

	if dbuser == "" || dbpass == "" || dbname == "" {
		error_log("Missing required environment variables: DB_USER, DB_PASS, DB_NAME")
		return
	}

	sessionTimeout, err := strconv.ParseInt(sessionTimeoutStr, 10, 64)
	if err != nil {
		error_log("Invalid SESSION_TIMEOUT value, using default")
		sessionTimeout = 1800
	}

	dsn := fmt.Sprintf("%s:%s@tcp(%s)/%s?parseTime=true", dbuser, dbpass, dbhost, dbname)
	db, err := sql.Open("mysql", dsn)
	if err != nil {
		error_log(fmt.Sprintf("Error connecting to the database: %v", err))
		return
	}
	defer db.Close()

	// Handle system signals for graceful shutdown
	stopChan := make(chan os.Signal, 1)
	signal.Notify(stopChan, os.Interrupt, syscall.SIGTERM)

	log.Printf("Session monitoring daemon (version %s) started...", version)
	log.Printf("Inactive users will be removed after %s days", inactiveDaysStr)

	// Daemon loop
	for {
		select {
		case <-stopChan:
			log.Println("Stopping daemon...")
			return
		default:
			cleanupExpiredSessions(db, sessionTimeout)
			cleanupUnverifiedUsers(db)
			cleanupInactiveUsers(db)
			time.Sleep(checkInterval * time.Second)
		}
	}
}
