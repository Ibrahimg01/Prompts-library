/**
 * Prompts Library Frontend JavaScript
 */

(function($) {
    'use strict';

    const PromptsLibrary = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // View prompt button
            $(document).on('click', '.view-prompt', this.openModal.bind(this));

            // Use prompt button (from card)
            $(document).on('click', '.use-prompt', this.usePromptDirect.bind(this));

            // Use prompt button (from modal)
            $(document).on('click', '.use-prompt-modal', this.usePromptFromModal.bind(this));

            // Copy prompt button
            $(document).on('click', '.copy-prompt', this.copyPrompt.bind(this));

            // Close modal
            $(document).on('click', '.modal-close, .modal-overlay', this.closeModal.bind(this));

            // Prevent modal close when clicking inside modal content
            $(document).on('click', '.modal-content', function(e) {
                e.stopPropagation();
            });

            // Close modal on ESC key
            $(document).keydown(function(e) {
                if (e.key === 'Escape') {
                    PromptsLibrary.closeModal();
                }
            });
        },

        /**
         * Open modal and load prompt details
         */
        openModal: function(e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const promptId = $button.data('prompt-id');

            // Show loading state
            $button.addClass('loading');

            this.requestPromptDetails(
                promptId,
                function(data) {
                    PromptsLibrary.displayModal(data);
                },
                function(message) {
                    alert(message || 'Error loading prompt');
                },
                function() {
                    $button.removeClass('loading');
                }
            );
        },

        /**
         * Shared helper to fetch prompt data via AJAX
         */
        requestPromptDetails: function(promptId, onSuccess, onError, onComplete) {
            $.ajax({
                url: promptsLibrary.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_prompt_details',
                    prompt_id: promptId,
                    nonce: promptsLibrary.nonce
                },
                success: function(response) {
                    if (response && response.success) {
                        if (typeof onSuccess === 'function') {
                            onSuccess(response.data);
                        }
                    } else if (typeof onError === 'function') {
                        const message = response && response.data ? response.data.message : null;
                        onError(message);
                    }
                },
                error: function() {
                    if (typeof onError === 'function') {
                        onError('Error loading prompt. Please try again.');
                    }
                },
                complete: function() {
                    if (typeof onComplete === 'function') {
                        onComplete();
                    }
                }
            });
        },

        /**
         * Display modal with prompt data
         */
        displayModal: function(data) {
            const $modal = $('#prompt-modal');
            
            // Set category badge
            if (data.categories && data.categories.length > 0) {
                const category = data.categories[0];
                $('.modal-category-badge')
                    .text(category.name)
                    .css('background-color', category.color);
            }

            // Set title and description
            $('.modal-title').text(data.title);
            $('.modal-description').text(data.description);

            // Set prompt text
            const normalizedPrompt = this.normalizePromptText(data.prompt_text);
            $('.prompt-text').text(normalizedPrompt);

            // Store prompt text for later use
            $modal.data('prompt-text', normalizedPrompt);

            // Show modal
            $modal.fadeIn(300);
            $('body').css('overflow', 'hidden');
        },

        /**
         * Close modal
         */
        closeModal: function() {
            $('#prompt-modal').fadeOut(300);
            $('body').css('overflow', 'auto');
        },

        /**
         * Use prompt directly (from card button)
         */
        usePromptDirect: function(e) {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const promptText = this.getPromptFromElement($button);

            if (promptText) {
                this.insertPromptToChatbot(promptText);
                return;
            }

            const promptId = $button.data('prompt-id');
            if (!promptId) {
                console.warn('[Prompts Library] Missing prompt identifier.');
                return;
            }

            $button.addClass('loading');

            this.requestPromptDetails(
                promptId,
                function(data) {
                    const normalized = PromptsLibrary.normalizePromptText(data.prompt_text);
                    PromptsLibrary.insertPromptToChatbot(normalized);
                },
                function(message) {
                    alert(message || 'Error loading prompt');
                },
                function() {
                    $button.removeClass('loading');
                }
            );
        },

        /**
         * Use prompt from modal
         */
        usePromptFromModal: function(e) {
            e.preventDefault();
            const $modal = $('#prompt-modal');
            const promptText = this.normalizePromptText($modal.data('prompt-text'));

            if (promptText) {
                this.insertPromptToChatbot(promptText);
                this.closeModal();
            }
        },

        /**
         * Retrieve prompt payload from a button/card dataset
         */
        getPromptFromElement: function($element) {
            if (!$element || $element.length === 0) {
                return '';
            }

            let prompt = $element.data('prompt');

            if (!prompt) {
                const $card = $element.closest('.pl-card');
                if ($card.length) {
                    prompt = $card.data('prompt');
                }
            }

            if (!prompt) {
                return '';
            }

            return this.normalizePromptText(prompt);
        },

        /**
         * Normalize prompt text (quotes/newlines)
         */
        normalizePromptText: function(promptText) {
            if (!promptText) {
                return '';
            }

            return String(promptText)
                .replace(/\r\n/g, '\n')
                .replace(/[“”]/g, '"')
                .replace(/[‘’]/g, "'");
        },

        /**
         * Insert prompt into chatbot
         */
        insertPromptToChatbot: function(promptText) {
            const text = this.normalizePromptText(promptText);

            // Try multiple selectors for different chatbot implementations
            const selectors = [
                'textarea[name="mwai_chat_input"]',
                '.mwai-input textarea',
                '#mwai-chat-input',
                'textarea.mwai-input',
                '.chatbot-input textarea',
                'input[type="text"].mwai-input'
            ];

            let $input = null;
            
            for (let selector of selectors) {
                $input = $(selector);
                if ($input.length > 0) {
                    break;
                }
            }

            if ($input && $input.length > 0) {
                // Set the value
                $input.val(text);
                
                // Trigger events to ensure the chatbot recognizes the input
                $input.trigger('input');
                $input.trigger('change');
                $input.focus();

                // Scroll to chatbot
                $('html, body').animate({
                    scrollTop: $input.offset().top - 100
                }, 500);

                // Show success message
                this.showSuccessMessage('Prompt loaded into chatbot!');
            } else {
                // If chatbot not found, copy to clipboard as fallback
                this.copyToClipboard(text);
                this.showSuccessMessage('Chatbot input not found. Prompt copied to clipboard!');
            }
        },

        /**
         * Copy prompt to clipboard
         */
        copyPrompt: function(e) {
            e.preventDefault();
            const $modal = $('#prompt-modal');
            const promptText = this.normalizePromptText($modal.data('prompt-text'));

            if (promptText) {
                PromptsLibrary.copyToClipboard(promptText);
                PromptsLibrary.showSuccessMessage('Prompt copied to clipboard!');
            }
        },

        /**
         * Copy text to clipboard
         */
        copyToClipboard: function(text) {
            // Create temporary textarea
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            
            try {
                document.execCommand('copy');
            } catch (err) {
                console.error('Failed to copy:', err);
            }
            
            $temp.remove();
        },

        /**
         * Show success message
         */
        showSuccessMessage: function(message) {
            // Remove any existing success messages
            $('.copy-success').remove();

            // Create and show new message
            const $success = $('<div class="copy-success">' + message + '</div>');
            $('body').append($success);

            // Remove after 3 seconds
            setTimeout(function() {
                $success.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        PromptsLibrary.init();
    });

})(jQuery);
