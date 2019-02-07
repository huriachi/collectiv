import axios from 'axios';

export class User {
    /**
     * Confirms and then resets the form that the event target resides in.
     *
     * @param event
     */
    resetForm(event) {
        Modal.display('Reset Form', 'Are you sure you want to undo all your entries?', () => {
            event.target.parentElement.reset();
            this.clearValidation(event.target.parentElement.elements);
        });
    }

    /**
     * Clears all Bootstrap validation classes from the given form elements. This also resets the validation text that
     * displays below the inputs.
     *
     * @param elements
     */
    clearValidation(elements) {
        for (let i = 0; i < elements.length; i++) {
            elements[i].classList.remove('is-valid', 'is-invalid');
            const feedbackElement = elements[i].parentElement.getElementsByClassName('invalid-feedback');
            if (feedbackElement.length > 0) {
                feedbackElement[0].innerHTML = '';
            }
        }
    }

    /**
     * Closely associated with the create and edit form. Handles form submission and the displaying of form success or
     * error status.
     *
     * @param event
     * @param url
     */
    sendForm(event, url, resetOnSubmit = true) {
        let formData = new FormData(event.target);

        axios.post(url, formData, {headers: {'Content-Type': 'multipart/form-data'}})
            .then((response) => {
                if (resetOnSubmit) {
                    event.target.reset();
                }
                Notification.success(response.data.message);
                this.clearValidation(event.target.elements);
            })
            .catch((error) => {
                const errors = error.response.data.errors;
                const elements = event.target.elements;

                Notification.error(error.response.data.message);

                // Attach the appropriate bootstrap classes to the form inputs.
                for (let i = 0; i < elements.length; i++) {
                    elements[i].classList.remove('is-valid', 'is-invalid');

                    if (errors[elements[i].id] !== undefined) {
                        elements[i].classList.add('is-invalid');
                    } else {
                        elements[i].classList.add('is-valid');
                    }

                    elements[i].parentElement
                        .getElementsByClassName('invalid-feedback')[0]
                        .innerHTML = errors[elements[i].id];
                }
            });
    }

    /**
     * AJAX request that allows for the deletion of a user. The action will be confirmed before execution.
     *
     * @param id
     */
    delete(id) {
        Modal.display(
            'Confirm User Deletion',
            `Are you sure you want to delete user with ID ${id}?`,
            () => {
                axios.post('/users/' + id + '/delete')
                    .then(() => {
                        window.location.reload();
                    });
            });
    }

    /**
     * Resets the database to its initial values.
     */
    resetDatabase() {
        Modal.display(
          'Confirm Database Reset',
          'The database will return to its original state. This cannot be undone. Are you sure?',
            () => {
              axios.post('/dangerous/database/reset')
                  .then(() => {
                      window.location.reload();
                  });
            }
        );
    }
}

export class Modal {
    /**
     * Displays a modal to the user to confirm something.
     *
     * @param title
     * @param content
     * @param action
     */
    static display(title, content, action) {
        $('#mainModal').modal({show: true, backdrop: true, keyboard: true});
        $('#mainModal .modal-title').text(title);
        $('#mainModal .modal-body p').text(content);

        $('#mainModal #mainModalConfirm').click(action);
    }
}

export class Notification {
    /**
     * Display a friendly green themed success message to the user.
     *
     * @param content
     * @param displayTime
     */
    static success(content, displayTime = 2000) {
        Notification.display(content, displayTime, 'SUCCESS');
    }

    /**
     * Display a menacing red themed error message to the user.
     *
     * @param content
     * @param displayTime
     */
    static error(content, displayTime = 2000) {
        Notification.display(content, displayTime, 'ERROR');
    }

    /**
     * Internal display function that is used by the success and error functions.
     *
     * @param content
     * @param displayTime
     * @param type
     */
    static display(content, displayTime, type) {
        let notification = document.getElementById('notification');

        switch (type) {
            case 'SUCCESS':
                notification.classList.add('alert-success');
                break;
            case 'ERROR':
                notification.classList.add('alert-danger');
                break;
        }

        notification.innerHTML = content;
        Notification.toggleVisible();

        setTimeout(() => {
            Notification.toggleVisible();
            notification.innerHTML = '';
            notification.classList.remove('alert-danger', 'alert-success');
        }, displayTime);
    }

    /**
     * Ease-of-use function for the toggling of notification visibility.
     */
    static toggleVisible() {
        let notification = document.getElementById('notification');
        notification.classList.toggle('visible');
        notification.classList.toggle('invisible');
    }
}