//Perfumers AI Chatbot
//Created by: JB Parfum
// Handles the chatbot modal, message sending, and response handling
// for a Perfumers AI assistant on the webpage.
// It includes a blinking cursor effect, toggles the chatbot modal, and manages chat history
// using local storage.

$(document).ready(function () {
    // Optional blinking cursor effect
    setInterval(() => {
        $('.blinking-cursor').css('visibility', function (_, visibility) {
            return visibility === 'hidden' ? 'visible' : 'hidden';
        });
    }, 500);

    $('#chatbot-icon').on('click', toggleChatbotModal);
    $('#chatbot-close').on('click', toggleChatbotModal);
    $('#chatbot-send').on('click', sendMessage);

    // Trigger send button on Enter key press
    $('#chatbot-input').on('keypress', function (e) {
        if (e.which === 13) { // Enter key pressed
            $('#chatbot-send').click();
        }
    });

    // Restore chat box state and messages
    const chatBoxState = localStorage.getItem('chatBoxState');
    const chatMessages = localStorage.getItem('chatMessages');
    const modal = $('#chatbot-modal');
    const chatBody = $('#chatbot-modal-body');

    if (chatBoxState === 'open') {
        modal.css('display', 'block');
    }

    if (chatMessages) {
        chatBody.html(chatMessages);
        chatBody.find('.alert').each(function () {
            const timestamp = $(this).find('small').text();
            if (!timestamp) {
                const newTimestamp = $('<small></small>')
                    .addClass('text-muted d-block')
                    .text(new Date().toLocaleTimeString());
                $(this).append(newTimestamp);
            }
        });

        // Reattach copy functionality to copy icons
        chatBody.find('.copy-icon').off('click').on('click', function () {
            const textToCopy = $(this).siblings('.bot-message').text();
            navigator.clipboard.writeText(textToCopy).then(() => {
                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>Response copied to clipboard!');
                $('.toast-header').removeClass().addClass('toast-header alert-success');
            }).catch(err => {
                $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> Failed to copy:' + err);
                $('.toast-header').removeClass().addClass('toast-header alert-danger');
            });
            $('.toast').toast('show');
        });

        chatBody.scrollTop(chatBody.prop('scrollHeight'));
    } else {
        // Add default message if localStorage is empty
        //const defaultMessage = $('<p></p>')
         //   .text("What are you blending today?")
         //   .addClass('text-secondary');
        chatBody.append(defaultMessage);
        localStorage.setItem('chatMessages', chatBody.html());
    }

    function toggleChatbotModal() {
        const modal = $('#chatbot-modal');
        const input = $('#chatbot-input');
        const isModalOpen = modal.css('display') === 'block';

        if (isModalOpen) {
            modal.css('display', 'none');
            modal.css('z-index', '2000'); // Reset z-index when closed
        } else {
            modal.css('display', 'block');
            modal.css('z-index', '2000'); // Ensure it appears above other modals
            input.focus(); // Focus on the input field when opened
        }

        // Dynamically apply Bootstrap theme classes
        const isDarkTheme = $('body').hasClass('bg-dark');
        modal.toggleClass('bg-dark text-light', isDarkTheme);
        modal.toggleClass('bg-light text-dark', !isDarkTheme);

        localStorage.setItem('chatBoxState', isModalOpen ? 'closed' : 'open');
    }

    function sendMessage() {
        const input = $('#chatbot-input');
        const sendButton = $('#chatbot-send');
        const message = input.val().trim();

        if (message) {
            const chatBody = $('#chatbot-modal-body');
            const timestamp = new Date().toLocaleTimeString();
            const userMessage = $('<div></div>')
                .addClass('alert alert-secondary text-end')
                .html(`<span class="fw-bold fs-6">${message}</span><br><small class="text-muted">${timestamp}</small>`);
            chatBody.append(userMessage);
            input.val('');
            chatBody.scrollTop(chatBody.prop('scrollHeight'));

            // Save updated chat to localStorage
            localStorage.setItem('chatMessages', chatBody.html());

            // Disable input and button
            input.prop('disabled', true);
            sendButton.prop('disabled', true);

            // Show "thinking..." message
            const thinkingMessage = $('<p></p>').text("Thinking...").addClass('thinking-message');
            chatBody.append(thinkingMessage);
            chatBody.scrollTop(chatBody.prop('scrollHeight'));

            // Send message to ai.php and handle response
            $.ajax({
                url: '/core/core.php',
                type: 'POST',
                data: { 
                    action: 'aiChat',
                    message: message
                },
                dataType: 'json',
                success: function (response) {
                    try {
                        // Remove "thinking..." message
                        chatBody.find('.thinking-message').remove();

                        const data = response.success;
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(item => {
                                simulateTypingEffect(item.description, chatBody);
                            });
                        } else if (response.error) {
                            const errorDiv = $('<div></div>')
                                .addClass('alert alert-danger')
                                .text(response.error);
                            chatBody.append(errorDiv);
                        } else {
                            simulateTypingEffect("No relevant information found.", chatBody);
                        }
                        chatBody.scrollTop(chatBody.prop('scrollHeight'));
                    } catch (e) {
                        const errorAlert = $('<div></div>')
                            .addClass('alert alert-danger')
                            .text("Error parsing response: " + e.message);
                        chatBody.append(errorAlert);
                        chatBody.scrollTop(chatBody.prop('scrollHeight'));
                    }
                },
                error: function (xhr, status, error) {
                    // Remove "thinking..." message
                    chatBody.find('.thinking-message').remove();

                    const errorAlert = $('<div></div>')
                        .addClass('alert alert-danger')
                        .text("Error communicating with the server: " + status + " - " + error + " - " + xhr.responseText);
                    chatBody.append(errorAlert);
                    chatBody.scrollTop(chatBody.prop('scrollHeight'));
                },
                complete: function () {
                    // Re-enable input and button
                    input.prop('disabled', false);
                    sendButton.prop('disabled', false);
                }
            });
        }
    }

    function simulateTypingEffect(text, chatBody) {
        const botMessageContainer = $('<div></div>')
            .addClass('alert alert-primary bot-message-container');
        const timestamp = new Date();
        const botMessage = $('<p></p>')
            .addClass('bot-message fw-bold'); // Add fw-bold class
        const copyIcon = $('<i></i>')
            .addClass('bi bi-clipboard copy-icon')
            .attr('title', 'Copy to clipboard')
            .on('click', function () {
                navigator.clipboard.writeText(text).then(() => {
                    $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i>Response copied to clipboard!');
                    $('.toast-header').removeClass().addClass('toast-header alert-success');
                }).catch(err => {
                    $('#toast-title').html('<i class="fa-solid fa-circle-exclamation mx-2"></i> Failed to copy:' + err);
                    $('.toast-header').removeClass().addClass('toast-header alert-danger');
                });
                $('.toast').toast('show');
            });

        botMessageContainer.append(botMessage).append(copyIcon);
        chatBody.append(botMessageContainer);

        let charIndex = 0;

        function typeChar() {
            if (charIndex < text.length) {
                botMessage.text(botMessage.text() + text.charAt(charIndex));
                charIndex++;
                setTimeout(typeChar, 15); // Typing speed
            } else {
                const timestampElement = $('<small></small>')
                    .addClass('text-muted d-block')
                    .text(timestamp.toLocaleTimeString())
                    .attr('title', timestamp.toLocaleString()); // Add hover with full date
                botMessageContainer.append(timestampElement);
                chatBody.scrollTop(chatBody.prop('scrollHeight'));

                // Save updated chat to localStorage
                localStorage.setItem('chatMessages', chatBody.html());
            }
        }

        typeChar();
    }


});
