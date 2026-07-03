/**
 * Form submission protection
 * Prevents double-submission by disabling submit buttons and showing a spinner.
 * Exclude forms with data-no-protect attribute.
 */
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form:not([data-no-protect])');

    forms.forEach((form) => {
        let isSubmitting = false;

        form.addEventListener('submit', (e) => {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }

            isSubmitting = true;

            const submitButton = form.querySelector(
                'button[type="submit"], button:not([type="button"]):not([type="reset"]):not([type])'
            );

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.style.opacity = '0.7';
                submitButton.style.pointerEvents = 'none';

                const spinner = document.createElement('span');
                spinner.innerHTML =
                    '<svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                spinner.className = 'inline-flex mr-1';

                submitButton.insertBefore(spinner, submitButton.firstChild);
            }

            // Safety net: re-enable after 5 seconds in case of slow redirect
            setTimeout(() => {
                isSubmitting = false;

                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.style.opacity = '';
                    submitButton.style.pointerEvents = '';

                    const spinner = submitButton.querySelector('.inline-flex');
                    if (spinner) {
                        spinner.remove();
                    }
                }
            }, 5000);
        });
    });
});
