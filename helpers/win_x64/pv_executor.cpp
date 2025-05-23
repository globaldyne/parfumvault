// This script downloads a Docker Compose file from a specified URL, checks if Docker is installed and running, and then executes the Docker Compose command to build and run the containers. It also opens a web browser to a specified URL after execution.
// This script is designed to be run on macOS and uses libcurl for downloading the file.
// g++ -o pv_executor.exe compose_executor.cpp -lcurl
// x86_64-w64-mingw32-g++ -o pv_executor.exe pv_executor.cpp -lwininet

#include <iostream>
#include <cstdlib>
#include <fstream>
#include <string>
#include <windows.h>
#include <wininet.h>

#pragma comment(lib, "wininet.lib")

// Function to download a file using WinINet
bool downloadFile(const char* url, const char* outputFilePath) {
    HINTERNET hInternet = InternetOpen("pv_executor", INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, 0);
    if (!hInternet) {
        std::cerr << "Error: Failed to initialize WinINet." << std::endl;
        return false;
    }

    HINTERNET hUrl = InternetOpenUrl(hInternet, url, NULL, 0, INTERNET_FLAG_RELOAD, 0);
    if (!hUrl) {
        std::cerr << "Error: Failed to open URL: " << url << std::endl;
        InternetCloseHandle(hInternet);
        return false;
    }

    std::ofstream outputFile(outputFilePath, std::ios::binary);
    if (!outputFile.is_open()) {
        std::cerr << "Error: Unable to open file for writing: " << outputFilePath << std::endl;
        InternetCloseHandle(hUrl);
        InternetCloseHandle(hInternet);
        return false;
    }

    char buffer[4096];
    DWORD bytesRead;
    while (InternetReadFile(hUrl, buffer, sizeof(buffer), &bytesRead) && bytesRead > 0) {
        outputFile.write(buffer, bytesRead);
    }

    outputFile.close();
    InternetCloseHandle(hUrl);
    InternetCloseHandle(hInternet);

    return true;
}

// Function to check if the server is returning HTTP 200
bool isServerUp(const char* url) {
    HINTERNET hInternet = InternetOpen("pv_executor", INTERNET_OPEN_TYPE_DIRECT, NULL, NULL, 0);
    if (!hInternet) {
        std::cerr << "Error: Failed to initialize WinINet for server check." << std::endl;
        return false;
    }

    HINTERNET hUrl = InternetOpenUrl(hInternet, url, NULL, 0, INTERNET_FLAG_RELOAD, 0);
    if (!hUrl) {
        InternetCloseHandle(hInternet);
        return false;
    }

    DWORD statusCode = 0;
    DWORD statusCodeSize = sizeof(statusCode);
    HttpQueryInfo(hUrl, HTTP_QUERY_STATUS_CODE | HTTP_QUERY_FLAG_NUMBER, &statusCode, &statusCodeSize, NULL);

    InternetCloseHandle(hUrl);
    InternetCloseHandle(hInternet);

    return statusCode == 200;
}

int main() {
    // Check if Docker is installed and running
    int dockerCheck = system("docker info >nul 2>&1");
    if (dockerCheck != 0) {
        std::cerr << "Error: Docker is not installed or not running. Please ensure Docker is installed and running." << std::endl;
        return 1;
    }

    // URL of the compose file
    const char* composeFileUrl = "https://raw.githubusercontent.com/globaldyne/parfumvault/master/docker-compose/compose.yaml";
    const char* localComposeFile = "compose.yaml";

    // Download the compose file using WinINet
    if (!downloadFile(composeFileUrl, localComposeFile)) {
        std::cerr << "Error: Failed to download compose file." << std::endl;
        return 1;
    }

    std::cout << "Compose file downloaded successfully." << std::endl;

    // Build the Docker Compose command
    std::string command = "docker-compose -f ";
    command += localComposeFile;
    command += " up --build -d";

    // Execute the command
    std::cout << "Executing: " << command << std::endl;
    int result = system(command.c_str());

    // Check the result
    if (result == 0) {
        std::cout << "Docker Compose executed successfully." << std::endl;

        // Check if the server is up
        const char* serverUrl = "http://localhost:8000";
        std::cout << "Checking if server is up at " << serverUrl << "..." << std::endl;

        bool serverUp = false;
        for (int i = 0; i < 10; ++i) {
            if (isServerUp(serverUrl)) {
                serverUp = true;
                break;
            }
            std::cout << "Server not up yet. Retrying in 10 seconds..." << std::endl;
            Sleep(10000); // Wait for 10 seconds
        }

        if (serverUp) {
            std::cout << "Server is up. Opening browser to " << serverUrl << "..." << std::endl;
            int browserResult = system("start http://localhost:8000"); // For Windows
            if (browserResult != 0) {
                std::cerr << "Warning: Failed to open browser. Please navigate to " << serverUrl << " manually." << std::endl;
            }
        } else {
            std::cerr << "Error: Server did not respond with HTTP 200 after 10 attempts. Please check the server manually." << std::endl;
        }
    } else {
        std::cerr << "Error: Docker Compose execution failed with code " << result << "." << std::endl;
    }

    return result;
}
