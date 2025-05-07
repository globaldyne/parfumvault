// This script handles the chatbot modal, message sending, and response handling
// for a perfume AI assistant on the webpage.
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
        if (e.which === 13) { // Enter key code
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
        chatBody.scrollTop(chatBody.prop('scrollHeight'));
    }

    function toggleChatbotModal() {
        const modal = $('#chatbot-modal');
        modal.css('display', modal.css('display') === 'block' ? 'none' : 'block');

        // Dynamically apply Bootstrap theme classes
        const isDarkTheme = $('body').hasClass('bg-dark');
        modal.toggleClass('bg-dark text-light', isDarkTheme);
        modal.toggleClass('bg-light text-dark', !isDarkTheme);

        const isModalOpen = modal.css('display') === 'block';
        localStorage.setItem('chatBoxState', isModalOpen ? 'open' : 'closed');
    }

    function sendMessage() {
        const input = $('#chatbot-input');
        const message = input.val().trim();
        if (message) {
            const chatBody = $('#chatbot-modal-body');
            const userMessage = $('<p></p>').text(message).css('text-align', 'right');
            chatBody.append(userMessage);
            input.val('');
            chatBody.scrollTop(chatBody.prop('scrollHeight'));

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
                }
            });

            localStorage.setItem('chatMessages', chatBody.html());
        }
    }

    function simulateTypingEffect(text, chatBody) {
        const botMessage = $('<p></p>');
        chatBody.append(botMessage);
        let charIndex = 0;

        function typeChar() {
            if (charIndex < text.length) {
                botMessage.text(botMessage.text() + text.charAt(charIndex));
                charIndex++;
                setTimeout(typeChar, 50); // Adjust typing speed here
            } else {
                chatBody.scrollTop(chatBody.prop('scrollHeight'));
                localStorage.setItem('chatMessages', chatBody.html());
            }
        }

        typeChar();
    }
});
