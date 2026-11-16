import './bootstrap';
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('#studio-sidebar');
    const scrim = document.querySelector('[data-close-menu]');
    const menuButton = document.querySelector('[data-mobile-menu]');

    const setMenuOpen = (open) => {
        sidebar?.classList.toggle('is-open', open);
        scrim?.classList.toggle('is-open', open);
        menuButton?.setAttribute('aria-expanded', String(open));
    };

    menuButton?.addEventListener('click', () => {
        setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    scrim?.addEventListener('click', () => setMenuOpen(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setMenuOpen(false);
    });

    const siteMenuButton = document.querySelector('[data-site-menu]');
    const siteNav = document.getElementById('site-navigation');
    if (siteMenuButton && siteNav) {
        const setSiteMenu = (open) => {
            siteNav.classList.toggle('is-open', open);
            siteMenuButton.setAttribute('aria-expanded', String(open));
        };
        siteMenuButton.addEventListener('click', () => setSiteMenu(!siteNav.classList.contains('is-open')));
        siteNav.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setSiteMenu(false)));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setSiteMenu(false);
        });
        document.addEventListener('click', (event) => {
            if (siteNav.classList.contains('is-open') && !siteNav.contains(event.target) && !siteMenuButton.contains(event.target)) {
                setSiteMenu(false);
            }
        });
    }

    const ownerMenuButton = document.querySelector('[data-owner-menu]');
    const ownerNav = document.getElementById('owner-navigation');
    if (ownerMenuButton && ownerNav) {
        const setOwnerMenu = (open) => {
            ownerNav.classList.toggle('is-open', open);
            ownerMenuButton.setAttribute('aria-expanded', String(open));
        };
        ownerMenuButton.addEventListener('click', () => setOwnerMenu(!ownerNav.classList.contains('is-open')));
        ownerNav.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOwnerMenu(false)));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setOwnerMenu(false);
        });
    }

    document.querySelectorAll('[data-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('.notice')?.remove());
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = button.parentElement.querySelector('input');
            const isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';
            button.classList.toggle('is-visible', !isVisible);
            button.setAttribute('aria-label', `${isVisible ? 'Show' : 'Hide'} password`);
            button.setAttribute('aria-pressed', String(!isVisible));
        });
    });

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-open-modal]');
        if (opener) {
            document.getElementById(opener.dataset.openModal)?.showModal();
            return;
        }

        const closer = event.target.closest('[data-close-modal]');
        if (closer) {
            closer.closest('dialog')?.close();
            return;
        }

        if (event.target.matches('dialog.modal-dialog')) {
            event.target.close();
        }
    });

    document.querySelectorAll('.modal-dialog').forEach((dialog) => {
        if (dialog.querySelector('.field-error')) dialog.showModal();
    });

    const orderForm = document.querySelector('[data-order-form]');

    if (orderForm) {
        const picker = orderForm.querySelector('[data-customer-picker]');
        const customerId = orderForm.querySelector('[data-customer-id]');
        const customerFields = {
            name: orderForm.querySelector('[data-customer-name]'),
            phone: orderForm.querySelector('[data-customer-phone]'),
            gender: orderForm.querySelector('[data-customer-gender]'),
        };

        const updateCustomer = () => {
            const option = picker.selectedOptions[0];
            const isExistingCustomer = Boolean(option.value);

            customerId.value = isExistingCustomer ? option.value : '';
            customerFields.name.disabled = isExistingCustomer;
            customerFields.phone.disabled = isExistingCustomer;
            customerFields.name.required = !isExistingCustomer;
            customerFields.phone.required = !isExistingCustomer;

            customerFields.name.value = isExistingCustomer ? option.dataset.name : '';
            customerFields.phone.value = isExistingCustomer ? option.dataset.phone : '';

            if (customerFields.gender) {
                customerFields.gender.disabled = isExistingCustomer;
                customerFields.gender.required = !isExistingCustomer;
                if (isExistingCustomer && option.dataset.gender) {
                    customerFields.gender.value = option.dataset.gender;
                }
            }
        };

        picker?.addEventListener('change', updateCustomer);
        if (picker?.value) updateCustomer();

        const customSize = orderForm.querySelector('input[name="standard_size"][value="custom"]');
        const measurementPanel = orderForm.querySelector('[data-measurements]');
        const measurementInputs = [...orderForm.querySelectorAll('.measurement-input')];
        let measurementsEdited = measurementInputs.some((input) => input.value.trim() !== '');

        const toggleMeasurements = (enabled) => {
            measurementPanel.hidden = !enabled;
            measurementInputs.forEach((input) => {
                input.disabled = !enabled;
            });

            if (enabled && !measurementsEdited) {
                const customer = picker?.selectedOptions[0];
                const savedMeasurements = JSON.parse(customer?.dataset.measurements || '{}');
                const measurementUnit = customer?.dataset.measurementUnit || 'in';

                measurementInputs.forEach((input) => {
                    const measurementName = input.name.match(/\[([^\]]+)\]/)?.[1];
                    if (measurementName && savedMeasurements[measurementName] !== undefined) {
                        input.value = savedMeasurements[measurementName];
                    }
                });

                const savedUnit = orderForm.querySelector(`input[name="measurement_unit"][value="${measurementUnit}"]`);
                if (savedUnit) savedUnit.checked = true;
                orderForm.querySelectorAll('.measurement-unit-label').forEach((label) => {
                    label.textContent = measurementUnit.toUpperCase();
                });
            }
        };

        orderForm.querySelectorAll('input[name="standard_size"]').forEach((input) => {
            input.addEventListener('change', () => toggleMeasurements(input.value === 'custom'));
        });

        measurementInputs.forEach((input) => {
            input.addEventListener('input', () => {
                measurementsEdited = true;
            });
        });

        if (customSize?.checked) {
            toggleMeasurements(true);
        } else {
            toggleMeasurements(false);
        }

        picker?.addEventListener('change', () => {
            const previousMeasurements = new Map(
                measurementInputs.map((input) => [input.name, input.value]),
            );
            const preserveMeasurements = measurementsEdited
                && measurementInputs.some((input) => input.value.trim() !== '');
            measurementsEdited = false;
            if (customSize?.checked) toggleMeasurements(true);
            if (preserveMeasurements) {
                measurementInputs.forEach((input) => {
                    input.value = previousMeasurements.get(input.name);
                });
                measurementsEdited = true;
            }
        });

        orderForm.querySelectorAll('input[name="measurement_unit"]').forEach((input) => {
            input.addEventListener('change', () => {
                orderForm.querySelectorAll('.measurement-unit-label').forEach((label) => {
                    label.textContent = input.value.toUpperCase();
                });
            });
        });

        const priceInput = orderForm.querySelector('[data-order-total]');
        const advanceInput = orderForm.querySelector('[data-order-advance]');
        const balanceOutput = orderForm.querySelector('[data-order-balance]');

        const updateBalance = () => {
            const total = Number(priceInput.value) || 0;
            const advance = Number(advanceInput.value) || 0;
            balanceOutput.textContent = Math.max(0, total - advance).toLocaleString('en-PK');
        };

        priceInput?.addEventListener('input', updateBalance);
        advanceInput?.addEventListener('input', updateBalance);
        updateBalance();
    }

    document.querySelectorAll('.price-adjustment-form').forEach((pricingForm) => {
        const totalInput = pricingForm.querySelector('[data-price-total]');
        const advanceInput = pricingForm.querySelector('[data-price-advance]');
        const balance = pricingForm.querySelector('[data-price-balance]');

        const updateBalance = () => {
            const total = Number(totalInput.value) || 0;
            const advance = Number(advanceInput.value) || 0;
            balance.textContent = Math.max(0, total - advance).toLocaleString('en-PK');
        };

        totalInput.addEventListener('input', updateBalance);
        advanceInput.addEventListener('input', updateBalance);
    });

    const form = document.querySelector('[data-booking-form]');
    if (!form) return;

    const steps = [...form.querySelectorAll('[data-step]')];
    const stepButtons = [...document.querySelectorAll('[data-step-button]')];
    let activeStep = 0;

    const showStep = (index) => {
        activeStep = Math.max(0, Math.min(index, steps.length - 1));
        steps.forEach((step, stepIndex) => {
            step.classList.toggle('is-visible', stepIndex === activeStep);
        });
        stepButtons.forEach((button, buttonIndex) => {
            button.classList.toggle('is-current', buttonIndex === activeStep);
            button.classList.toggle('is-done', buttonIndex < activeStep);
            if (buttonIndex === activeStep) button.setAttribute('aria-current', 'step');
            else button.removeAttribute('aria-current');
        });
    };

    const validateStep = (stepIndex) => {
        const fields = [...steps[stepIndex].querySelectorAll('input:not([type=radio]), select')];
        const radios = [...steps[stepIndex].querySelectorAll('input[type=radio][required]')];
        const radioNames = [...new Set(radios.map((radio) => radio.name))];
        const invalidField = fields.find((field) => !field.disabled && !field.checkValidity());
        const missingChoice = radioNames.some((name) => {
            if (name === 'standard_size') {
                return !form.querySelector('input[name="standard_size"]:checked')
                    && ![...form.querySelectorAll('.measurement-input')].some((input) => input.value.trim() !== '');
            }

            return !form.querySelector(`input[name="${name}"]:checked`);
        });

        if (invalidField) {
            invalidField.reportValidity();
            invalidField.focus();
            return false;
        }

        if (missingChoice) {
            const first = radios.find((radio) => !form.querySelector(`input[name="${radio.name}"]:checked`));
            const message = first?.name === 'garment'
                ? 'Please pick the garment you have in mind.'
                : 'Please choose an option to continue.';
            const feedback = document.createElement('p');
            feedback.className = 'field-error step-feedback';
            feedback.setAttribute('role', 'alert');
            feedback.textContent = message;
            steps[stepIndex].querySelector('.step-feedback')?.remove();
            (first ? steps[stepIndex].querySelector(`.booking-fieldset:has(input[name="${first.name}"])`) : steps[stepIndex]).append(feedback);
            return false;
        }

        steps[stepIndex].querySelector('.step-feedback')?.remove();
        return true;
    };

    form.querySelectorAll('[data-next]').forEach((button) => {
        button.addEventListener('click', () => {
            if (validateStep(activeStep)) showStep(activeStep + 1);
        });
    });

    form.querySelectorAll('[data-previous]').forEach((button) => {
        button.addEventListener('click', () => showStep(activeStep - 1));
    });

    stepButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            if (index <= activeStep) showStep(index);
            else if (validateStep(activeStep)) showStep(index);
        });
    });

    const measurements = form.querySelector('[data-measurements]');
    const measurementInputs = [...form.querySelectorAll('.measurement-input')];

    const setMeasurementMode = (enabled) => {
        measurements.hidden = !enabled;
        measurementInputs.forEach((input) => {
            input.disabled = !enabled;
        });
        if (enabled) {
            form.querySelector('input[name="standard_size"][value="custom"]').checked = true;
        }
    };

    form.querySelectorAll('input[name="standard_size"]').forEach((input) => {
        input.addEventListener('change', () => setMeasurementMode(input.value === 'custom'));
    });

    if (measurements && !measurements.hidden) {
        measurementInputs.forEach((input) => {
            input.disabled = false;
        });
    }

    form.querySelectorAll('input[name="measurement_unit"]').forEach((input) => {
        input.addEventListener('change', () => {
            form.querySelectorAll('.measurement-unit-label').forEach((label) => {
                label.textContent = input.value.toUpperCase();
            });
        });
    });

    const startedOnStepThree = Boolean(form.querySelector('.notice-error, .field-error'));
    if (startedOnStepThree) {
        showStep(2);
        if (form.querySelector('input[name="standard_size"]:checked')?.value === 'custom') {
            setMeasurementMode(true);
        }
    } else {
        showStep(0);
    }

    form.addEventListener('submit', (event) => {
        if (!validateStep(2)) {
            event.preventDefault();
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.querySelector('[data-select-all]');
    const rowCheckboxes = [...document.querySelectorAll('[data-select-row]')];
    const bar = document.querySelector('[data-bulk-action-bar]');
    const countEl = bar?.querySelector('[data-bulk-count]');

    if (!selectAll || rowCheckboxes.length === 0 || !bar) return;

    const refresh = () => {
        const checked = rowCheckboxes.filter((box) => box.checked);
        bar.hidden = checked.length === 0;
        if (countEl) countEl.textContent = String(checked.length);
        selectAll.checked = checked.length === rowCheckboxes.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < rowCheckboxes.length;
    };

    selectAll.addEventListener('change', () => {
        rowCheckboxes.forEach((box) => { box.checked = selectAll.checked; });
        refresh();
    });

    rowCheckboxes.forEach((box) => box.addEventListener('change', refresh));

    document.getElementById('bulk-status-form')?.addEventListener('submit', (event) => {
        if (![...rowCheckboxes].some((box) => box.checked)) {
            event.preventDefault();
        }
    });

    refresh();
});
