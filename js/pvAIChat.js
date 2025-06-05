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
                    .addClass('text-chat-ai-footer d-block')
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
        //chatBody.append(defaultMessage);
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
                .html(`<span class="fw-bold fs-6">${message}</span><br><small class="text-chat-ai-footer">${timestamp}</small>`);
            
            // Add repeat button
            const repeatBtn = $('<button></button>')
                .addClass('btn btn-link btn-sm text-decoration-none repeat-prompt-btn')
                .attr('title', 'Repeat this prompt')
                .html('<i class="bi bi-arrow-repeat"></i>')
                .css({ float: 'left', padding: 0, margin: '0 0.5em 0 0' })
                .on('click', function (e) {
                    e.preventDefault();
                    input.val(message);
                    $('#chatbot-send').click();
                });
            userMessage.prepend(repeatBtn);

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
                        let type = (data && typeof data === 'object' && data.type) ? data.type : (response.type || 'unknown');

                        // If type is unknown, try to determine context from common fields
                        if (!type || type === 'unknown') {
                            // Check for common fields in the returned JSON
                            if (data && typeof data === 'object') {
                                if (data.response) {
                                    // If response is an object, check for description/content/text
                                    if (typeof data.response === 'object') {
                                        if (data.response.description) {
                                            type = 'ingredient';
                                        } else if (data.response.content) {
                                            type = 'general';
                                        } else if (data.response.text) {
                                            type = 'general';
                                        }
                                    } else if (typeof data.response === 'string') {
                                        type = 'general';
                                    }
                                } else if (data.description) {
                                    // If description is an array, treat as replacements, else ingredient/general
                                    if (Array.isArray(data.description)) {
                                        type = 'replacements';
                                    } else {
                                        type = 'ingredient';
                                    }
                                } else if (data.content) {
                                    type = 'general';
                                } else if (data.text) {
                                    type = 'general';
                                }
                            }
                        }

                        // Handle formula type: show as a simple table
                        if (type === 'formula' && data && typeof data === 'object' && Array.isArray(data.formula)) {
                            let html = `<div class="fw-bold mb-2">Formula:</div>`;
                            html += `<table class="table table-bordered table-sm mb-2"><thead><tr>
                                <th>Ingredient</th>
                                <th>CAS</th>
                                <th>Quantity</th>
                                <th>Dilution (%)</th>
                                <th>Solvent</th>
                            </tr></thead><tbody>`;
                            data.formula.forEach(row => {
                                html += `<tr>
                                    <td>${row.ingredient || ''}</td>
                                    <td>${row.cas || ''}</td>
                                    <td>${row.quantity || ''}</td>
                                    <td>${row.dilution || ''}</td>
                                    <td>${row.solvent || ''}</td>
                                </tr>`;
                            });
                            html += `</tbody></table>`;
                            if (data.total_quantity) {
                                html += `<div class="mb-2"><strong>Total Quantity:</strong> ${data.total_quantity}</div>`;
                            }
                            simulateTypingEffect(html, chatBody, true);
                        }
                        // Handle ingredient type (or default object with description)
                        else if (type === 'ingredient' && data && typeof data === 'object' && data.description) {
                            let html = `<div>${data.description}</div>`;
                            let details = [];
                            // Add CAS number if present
                            if (data.cas) {
                                details.push(`<li><i class="bi bi-flask me-2"></i><strong>CAS:</strong> ${data.cas}</li>`);
                            }
                            // Add IFRA limit if present
                            if (data.ifra_limit) {
                                details.push(`<li><i class="bi bi-exclamation-triangle me-2"></i><strong>IFRA Limit:</strong> ${data.ifra_limit}</li>`);
                            }
                            if (data.physical_state) {
                                details.push(`<li><i class="bi bi-droplet-half me-2"></i><strong>Physical State:</strong> ${data.physical_state}</li>`);
                            }
                            if (data.color) {
                                details.push(`<li><i class="bi bi-palette me-2"></i><strong>Color:</strong> ${data.color}</li>`);
                            }
                            if (data.category) {
                                details.push(`<li><i class="bi bi-tag me-2"></i><strong>Category:</strong> ${data.category}</li>`);
                            }
                            if (data.olfactory_type) {
                                details.push(`<li><i class="bi bi-flower1 me-2"></i><strong>Olfactory Type:</strong> ${data.olfactory_type}</li>`);
                            }
                            if (details.length > 0) {
                                html += `<ul class="mb-1 mt-2">${details.join('')}</ul>`;
                            }
                            simulateTypingEffect(html, chatBody, true);
                        }
                        // Handle replacements type: show as a group list
                        else if (type === 'replacements' && data && typeof data === 'object' && Array.isArray(data.replacements)) {
                            let html = `<div class="fw-bold mb-2">Replacement Suggestions:</div>`;
                            html += `<ul class="list-group mb-2">`;
                            data.replacements.forEach(row => {
                                html += `<li class="list-group-item">
                                    <strong>${row.ingredient || ''}</strong>
                                    ${row["CAS number"] ? `<span class="text-muted ms-2">(CAS: ${row["CAS number"]})</span>` : ""}
                                    <div>${row.properties ? `<em>${row.properties}</em>` : ""}</div>
                                    <div>${row.description || ''}</div>
                                </li>`;
                            });
                            html += `</ul>`;
                            simulateTypingEffect(html, chatBody, true);
                        }
                        // If type is general, just parse what's in response
                        else if (type === 'general' && typeof data === 'string') {
                            simulateTypingEffect(data, chatBody);
                        }
                        // If can't determine type, check for clarification prompt
                        else if (
                            (typeof data === 'string' && data.match(/clarify.*ingredient.*formula|are you asking.*ingredient.*formula|do you want a formula/i)) ||
                            (typeof data === 'object' && data !== null && data.response && typeof data.response === 'string' && data.response.match(/clarify.*ingredient.*formula|are you asking.*ingredient.*formula|do you want a formula/i))
                        ) {
                            simulateTypingEffect("Could you clarify your request? Are you asking about a perfume ingredient or do you want a formula generated?", chatBody);
                        }
                        // Handle multi-section object with numeric keys (AI summary, formula, replacements, general, etc.)
                        else if (
                            data && typeof data === 'object' &&
                            Object.keys(data).some(k => !isNaN(k))
                        ) {
                            // Sort keys numerically
                            const keys = Object.keys(data).filter(k => !isNaN(k)).sort((a, b) => a - b);
                            keys.forEach(k => {
                                const section = data[k];
                                if (!section || typeof section !== 'object') return;
                                // Ingredient section
                                if (section.type === 'ingredient') {
                                    let html = `<div class="fw-bold mb-2">${section.ingredient || 'Ingredient'}</div>`;
                                    if (section.cas) {
                                        html += `<div><span class="text-muted">CAS: ${section.cas}</span></div>`;
                                    }
                                    html += `<div>${section.description || ''}</div>`;
                                    if (section.properties && typeof section.properties === 'object') {
                                        html += `<ul class="mb-1 mt-2">`;
                                        for (const prop in section.properties) {
                                            html += `<li><strong>${prop.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</strong> ${section.properties[prop]}</li>`;
                                        }
                                        html += `</ul>`;
                                    }
                                    simulateTypingEffect(html, chatBody, true);
                                }
                                // Formula section
                                else if (section.type === 'formula') {
                                    let html = `<div class="fw-bold mb-2">${section.formula || 'Formula'}</div>`;
                                    html += `<div>${section.description || ''}</div>`;
                                    if (section.examples && Array.isArray(section.examples)) {
                                        html += `<ul class="mb-1 mt-2">`;
                                        section.examples.forEach(ex => html += `<li>${ex}</li>`);
                                        html += `</ul>`;
                                    }
                                    if (section.concentration) {
                                        html += `<div><strong>Concentration:</strong> ${section.concentration}</div>`;
                                    }
                                    simulateTypingEffect(html, chatBody, true);
                                }
                                // Replacements section
                                else if (section.type === 'replacements') {
                                    let html = `<div class="fw-bold mb-2">${section.replacements || 'Replacements'}</div>`;
                                    html += `<div>${section.description || ''}</div>`;
                                    if (section.replacements_list && Array.isArray(section.replacements_list)) {
                                        html += `<ul class="mb-1 mt-2">`;
                                        section.replacements_list.forEach(rep => html += `<li>${rep}</li>`);
                                        html += `</ul>`;
                                    }
                                    if (section.considerations) {
                                        html += `<div><strong>Considerations:</strong> ${section.considerations}</div>`;
                                    }
                                    simulateTypingEffect(html, chatBody, true);
                                }
                                // General section
                                else if (section.type === 'general') {
                                    let html = `<div class="fw-bold mb-2">${section.general || 'General'}</div>`;
                                    html += `<div>${section.description || ''}</div>`;
                                    if (section.safety) {
                                        html += `<div><strong>Safety:</strong> ${section.safety}</div>`;
                                    }
                                    if (section.storage) {
                                        html += `<div><strong>Storage:</strong> ${section.storage}</div>`;
                                    }
                                    simulateTypingEffect(html, chatBody, true);
                                }
                            });
                        }
                        // Handle formula object with nested ingredients array (e.g. data.formula.ingredients)
                        else if (
                            type === 'formula' &&
                            data && typeof data === 'object' &&
                            data.formula && typeof data.formula === 'object' &&
                            Array.isArray(data.formula.ingredients)
                        ) {
                            const formula = data.formula;
                            let html = `<div class="fw-bold mb-2">${formula.name ? 'Formula: ' + formula.name : 'Formula'}</div>`;
                            if (formula.description) {
                                html += `<div class="mb-2">${formula.description}</div>`;
                            }
                            html += `<table class="table table-bordered table-sm mb-2"><thead><tr>
                                <th>Ingredient</th>
                                <th>CAS</th>
                                <th>Quantity</th>
                                <th>Dilution (%)</th>
                                <th>Solvent</th>
                                <th>Properties</th>
                                <th>Olfactory Type</th>
                            </tr></thead><tbody>`;
                            formula.ingredients.forEach(row => {
                                html += `<tr>
                                    <td>${row.ingredient || ''}</td>
                                    <td>${row.cas || ''}</td>
                                    <td>${row.quantity || ''}</td>
                                    <td>${row.dilution || ''}</td>
                                    <td>${row.solvent || ''}</td>
                                    <td>${Array.isArray(row.properties) ? row.properties.join(', ') : (row.properties || '')}</td>
                                    <td>${row.olfactory_type || ''}</td>
                                </tr>`;
                            });
                            html += `</tbody></table>`;
                            if (formula.total_quantity) {
                                html += `<div class="mb-2"><strong>Total Quantity:</strong> ${formula.total_quantity}</div>`;
                            }
                            simulateTypingEffect(html, chatBody, true);
                        }
                        // Handle array of objects (fallback)
                        else if (Array.isArray(data) && data.length > 0) {
                            data.forEach(item => {
                                if (item.description && item.description.trim() !== "") {
                                    simulateTypingEffect(item.description, chatBody);
                                } else if (item.answer && item.answer.trim() !== "") {
                                    simulateTypingEffect(item.answer, chatBody);
                                } else {
                                    simulateTypingEffect("No relevant information found.", chatBody);
                                }
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

    function simulateTypingEffect(text, chatBody, isHtml = false) {
        const botMessageContainer = $('<div></div>')
            .addClass('alert alert-primary bot-message-container');
        const timestamp = new Date();
        const botMessage = $('<p></p>')
            .addClass('bot-message fw-bold');
        const copyIcon = $('<i></i>')
            .addClass('bi bi-clipboard copy-icon')
            .attr('title', 'Copy to clipboard')
            .on('click', function () {
                // Strip HTML tags for copying
                const tempDiv = $('<div>').html(text);
                navigator.clipboard.writeText(tempDiv.text()).then(() => {
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
        let plainText = isHtml ? $('<div>').html(text).text() : text;

        function typeChar() {
            if (charIndex < plainText.length) {
                if (isHtml) {
                    // Show full HTML after typing effect is done
                    botMessage.text(botMessage.text() + plainText.charAt(charIndex));
                } else {
                    botMessage.text(botMessage.text() + text.charAt(charIndex));
                }
                charIndex++;
                setTimeout(typeChar, 15);
            } else {
                if (isHtml) {
                    botMessage.html(text); // Replace with HTML after typing
                }
                const timestampElement = $('<small></small>')
                    .addClass('text-chat-ai-footer d-block')
                    .text(timestamp.toLocaleTimeString())
                    .attr('title', timestamp.toLocaleString());
                botMessageContainer.append(timestampElement);
                chatBody.scrollTop(chatBody.prop('scrollHeight'));
                localStorage.setItem('chatMessages', chatBody.html());
            }
        }

        typeChar();
    }


});
