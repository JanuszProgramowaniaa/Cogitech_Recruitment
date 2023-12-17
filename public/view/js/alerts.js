if (!alertPlaceholder) {
    const alertPlaceholder = document.createElement('div');
    alertPlaceholder.id = 'alertPlaceholder';
    document.body.appendChild(alertPlaceholder);
}

/**
 * @param {string} message - message to show
 * @param {string} type - alert type, available alerts types - [primary, secondary, success, danger, warning, info, light, dark]
 */
const showAlert = (message, type) => {
    const wrapper = document.createElement('div');
    wrapper.classList.add('alert', `alert-${type}`, 'alert-dismissible', 'fade', 'show');

    wrapper.innerHTML = `
        <div class="d-flex justify-content-center">${message}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    alertPlaceholder.appendChild(wrapper);

    // hide alert after 3s
    hideAlert(wrapper, 3000);
}

/**
 * @param {HTMLElement} alertElement - alert element to hide
 * @param {number} timeout - alert auto close time in ms
 */
const hideAlert = (alertElement, timeout) => {
    setTimeout(() => {
        alertElement.remove();
    }, timeout);
};
