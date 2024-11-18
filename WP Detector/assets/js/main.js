// WordPress Theme & Plugin Detector JavaScript
(() => {
    'use strict';

    const CLASSES = {
        HIDDEN: 'hidden',
        FADE_IN: 'fade-in',
        ANIMATE_FADE_IN: 'animate-fade-in'
    };

    const TAILWIND = {
        TABLE: ['w-full', 'mb-6', 'border-collapse', 'bg-white', 'shadow-sm', 'rounded-lg', 'overflow-hidden'],
        ROW: ['border-b', 'border-gray-200'],
        CELL: ['px-6', 'py-4'],
        HEADING: ['text-xl', 'font-semibold', 'text-gray-800', 'mb-4', 'mt-8'],
        SUCCESS_MESSAGE: ['bg-green-100', 'text-green-800', 'p-4', 'rounded-lg', 'mb-6'],
        ERROR_MESSAGE: ['bg-red-100', 'text-red-800', 'p-4', 'rounded-lg', 'mb-6'],
        RECOMMENDATIONS: ['bg-yellow-50', 'border', 'border-yellow-200', 'rounded-lg', 'p-4', 'mb-6'],
        LIST: ['list-disc', 'pl-5', 'space-y-2', 'mt-2']
    };

    const elements = {
        form: document.getElementById('analyzeForm'),
        loading: document.getElementById('loading'),
        result: document.getElementById('result'),
        input: document.querySelector('input[type="url"]'),
        goToTop: document.getElementById('goToTop')
    };

    const applyClasses = (element, classes) => element.classList.add(...classes);

    const styleElements = (container) => {
        const selectors = {
            'table': TAILWIND.TABLE,
            'tr': TAILWIND.ROW,
            'td': TAILWIND.CELL,
            'h3, h4': TAILWIND.HEADING,
            '.success-message': TAILWIND.SUCCESS_MESSAGE,
            '.error-message': TAILWIND.ERROR_MESSAGE,
            '.security-recommendations, .performance-recommendations': TAILWIND.RECOMMENDATIONS,
            '.security-recommendations ul, .performance-recommendations ul': TAILWIND.LIST
        };

        Object.entries(selectors).forEach(([selector, classes]) => {
            container.querySelectorAll(selector).forEach(el => applyClasses(el, classes));
        });
    };

    const setupAnimations = (() => {
        const observer = new IntersectionObserver(
            entries => entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(CLASSES.ANIMATE_FADE_IN);
                }
            }),
            { threshold: 0.1 }
        );

        return (container) => {
            container.querySelectorAll('div, table').forEach(element => {
                observer.observe(element);
            });
        };
    })();

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        try {
            elements.loading.classList.remove(CLASSES.HIDDEN);
            elements.result.innerHTML = '';
            
            const response = await fetch('check_wordpress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `url=${encodeURIComponent(elements.input.value)}`
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.text();
            
            elements.loading.classList.add(CLASSES.HIDDEN);
            elements.result.innerHTML = data;
            elements.result.classList.add(CLASSES.FADE_IN);

            styleElements(elements.result);
            setupAnimations(elements.result);

            if (window.history?.replaceState) {
                window.history.replaceState({}, document.title, window.location.href.split('?')[0]);
            }

            elements.form.reset();
            
        } catch (error) {
            console.error('Error:', error);
            elements.loading.classList.add(CLASSES.HIDDEN);
            elements.result.innerHTML = `
                <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">
                    Error: ${error.message}
                </div>
            `;
        }
    };

    // Initialize
    const init = () => {
        // Set up form submission handler
        elements.form.addEventListener('submit', handleSubmit);

        ['focus', 'blur'].forEach(event => {
            elements.input.addEventListener(event, () => {
                elements.input.parentElement.classList.toggle('ring-2');
                elements.input.parentElement.classList.toggle('ring-primary');
                elements.input.parentElement.classList.toggle('ring-opacity-50');
            });
        });

        // Go to Top button functionality
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY > 200;
            elements.goToTop.classList.toggle('opacity-0', !scrolled);
            elements.goToTop.classList.toggle('invisible', !scrolled);
        });

        elements.goToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    };

    // Start the application
    document.addEventListener('DOMContentLoaded', init);
})();