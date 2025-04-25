// This script downloads a Docker Compose file from a specified URL, checks if Docker is installed and running, and then executes the Docker Compose command to build and run the containers. It also opens a web browser to a specified URL after execution.
// This script is designed to be run on macOS and uses libcurl for downloading the file.
// g++ -o pv_executor pv_executor.cpp -lcurl

#include <iostream>
#include <cstdlib>
#include <fstream>
#include <string>
#include <curl/curl.h>

// Callback function for libcurl to write data to a file
size_t writeToFile(void* ptr, size_t size, size_t nmemb, FILE* stream) {
    return fwrite(ptr, size, nmemb, stream);
}

int main() {
    // Check if Docker is installed and running
    int dockerCheck = system("docker info >/dev/null 2>&1");
    if (dockerCheck != 0) {
        std::cerr << "Error: Docker is not installed or not running. Please ensure Docker is installed and running." << std::endl;
        return 1;
    }

    // URL of the compose file
    const char* composeFileUrl = "https://raw.githubusercontent.com/globaldyne/parfumvault/master/docker-compose/compose.yaml";

    // Download the compose file using libcurl
    CURL* curl = curl_easy_init();
    if (curl) {
        FILE* file = fopen("compose.yaml", "wb");
        if (!file) {
            std::cerr << "Error: Unable to open file for writing: compose.yaml" << std::endl;
            return 1;
        }

        curl_easy_setopt(curl, CURLOPT_URL, composeFileUrl);
        curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, writeToFile);
        curl_easy_setopt(curl, CURLOPT_WRITEDATA, file);

        CURLcode res = curl_easy_perform(curl);
        fclose(file);
        curl_easy_cleanup(curl);

        if (res != CURLE_OK) {
            std::cerr << "Error: Failed to download compose file. CURL error: " << curl_easy_strerror(res) << std::endl;
            return 1;
        }

        std::cout << "Compose file downloaded successfully." << std::endl;
    } else {
        std::cerr << "Error: Failed to initialize CURL." << std::endl;
        return 1;
    }

    // Build the Docker Compose command
    std::string command = "docker-compose -f compose.yaml up --build -d";

    // Execute the command
    std::cout << "Executing: " << command << std::endl;
    int result = system(command.c_str());

    // Check the result
    if (result == 0) {
        std::cout << "Docker Compose executed successfully." << std::endl;

        // Open a browser to http://localhost:8000
        std::cout << "Opening browser to http://localhost:8000..." << std::endl;
        int browserResult = system("open http://localhost:8000"); // For macOS
        if (browserResult != 0) {
            std::cerr << "Warning: Failed to open browser. Please navigate to http://localhost:8000 manually." << std::endl;
        }
    } else {
        std::cerr << "Error: Docker Compose execution failed with code " << result << "." << std::endl;
    }

    return result;
}
