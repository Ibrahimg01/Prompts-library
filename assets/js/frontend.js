/**
 * Prompts Library Frontend JavaScript
 */

(function () {
    'use strict';

    function normalizeText(s) {
        if (!s) {
            return '';
        }

        return String(s)
            .replace(/\r\n/g, '\n')
            .replace(/[“”]/g, '"')
            .replace(/[‘’]/g, "'");
    }

    function getPrompt(btn) {
        var element = btn || null;
        var value = '';

        if (element && element.dataset && element.dataset.prompt) {
            value = element.dataset.prompt;
        }

        if (!value && element && element.closest) {
            var card = element.closest('.pl-card');
            if (card && card.dataset) {
                value = card.dataset.prompt || '';
            }
        }

        return normalizeText(value);
    }

    function insertIntoChat(text) {
        var ta = document.querySelector('.mwai-chatbot textarea, #mwai_chatbot textarea, .mwai-input textarea');

        if (ta) {
            ta.focus();
            ta.value = text;
            ta.dispatchEvent(new Event('input', { bubbles: true }));
            ta.dispatchEvent(new Event('change', { bubbles: true }));
            return true;
        }

        console.warn('[PL] Chat input not found on this page.');
        return false;
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.pl-use-prompt');
        if (!btn) {
            return;
        }

        e.preventDefault();

        var text = getPrompt(btn);

        if (!text) {
            console.warn('[PL] Empty data-prompt. Check that PHP set the right meta key for prompt text.');
            return;
        }

        insertIntoChat(text);
    });
})();
