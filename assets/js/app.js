/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

import axios from 'axios';
import JSEncrypt from 'jsencrypt';

function download(data, fileName, mimeType) {
    const fakeLink = document.createElement('a');
    mimeType = mimeType || 'application/octet-stream';
    fileName = fileName || 'download.file';

    if (navigator.msSaveBlob) {
        navigator.msSaveBlob(new Blob([data], { type: mimeType }), fileName);
    } else if (URL && 'download' in fakeLink) {
        fakeLink.href = URL.createObjectURL(new Blob([data], { type: mimeType }));
        fakeLink.setAttribute('download', fileName);
        document.body.appendChild(fakeLink);
        fakeLink.click();
        document.body.removeChild(fakeLink);
    } else {
        location.href = 'data:application/octet-stream,' + encodeURIComponent(data);
    }
}

document.getElementById('form-new-registration').addEventListener("submit", (event) => {
    event.preventDefault();

    const publicKey = document.getElementById('form-new-registration__public-key').value;
    const plainData = JSON.stringify({
        firstName: document.getElementById('form-new-registration__first-name').value,
        lastName: document.getElementById('form-new-registration__last-name').value,
    });

    if (!publicKey) {
        alert('Missing public key to encrypt data.');
        return false;
    }

    const crypto = new JSEncrypt();
    crypto.setPublicKey(publicKey);

    const encryptedData = crypto.encrypt(plainData);

    const payload = {
        payload: encryptedData,
    };

    (async () => {
        event.target.querySelector('button').disabled = true;

        try {
            await axios
                .post(event.target.action, payload)
                .then(
                    (result) => {
                        if (result.data.location || false) {
                            window.location.href = result.data.location;
                        }
                        return Promise.resolve(result.data);
                    },
                    (error) => Promise.reject(error),
                )
                .catch((reason) => Promise.reject(reason))
            ;
        } catch (error) {
            alert(error);
        } finally {
            event.target.querySelector('button').disabled = false;
        }
    })();

    return false;
});

document.querySelectorAll('button[data-action]').forEach((button) => {
    button.addEventListener("click", (event) => {
        event.preventDefault();

        const action = button.dataset.action;
        const id = button.dataset.id;

        (async () => {
            event.target.parentNode.querySelectorAll('button').forEach((button) => button.disabled = true);
            try {
                if (action === 'delete') {
                    await axios
                        .delete('/registration/{id}'.replace('{id}', id))
                        .then(
                            (result) => {
                                if (result.data.location || false) {
                                    window.location.href = result.data.location;
                                }
                                return Promise.resolve(result.data);
                            },
                            (error) => Promise.reject(error),
                        )
                        .catch((reason) => Promise.reject(reason))
                    ;
                }
                if (action === 'checkout') {
                    await axios
                        .post('/registration/{id}/checkout'.replace('{id}', id), {})
                        .then(
                            (result) => {
                                if (result.data.location || false) {
                                    window.location.href = result.data.location;
                                }
                                return Promise.resolve(result.data);
                            },
                            (error) => Promise.reject(error),
                        )
                        .catch((reason) => Promise.reject(reason))
                    ;
                }
                if (action === 'decrypt') {
                    const privateKey = document.getElementById('decrypt-private-key').value;
                    if (!privateKey) {
                        alert('Missing private key to decrypt data.');
                        return;
                    }

                    const crypto = new JSEncrypt();
                    crypto.setPrivateKey(privateKey);

                    const response = await axios
                        .get('/registration/{id}'.replace('{id}', id))
                        .then(
                            (result) => Promise.resolve(result.data),
                            (error) => Promise.reject(error),
                        )
                        .catch((reason) => Promise.reject(reason))
                    ;

                    const decryptedData = crypto.decrypt(response.payload);

                    if (decryptedData === false) {
                        alert('Invalid private key. Does not match to public key used to encrypt the data.');
                        return;
                    }

                    const fullRecord = {
                        ...response,
                        payload: JSON.parse(decryptedData),
                    };

                    download(JSON.stringify(fullRecord, null, 2), `${id}.decrypted.json`, 'application/json');
                }
                if (action === 'download') {
                    const response = await axios
                        .get('/registration/{id}'.replace('{id}', id))
                        .then(
                            (result) => Promise.resolve(result.data),
                            (error) => Promise.reject(error),
                        )
                        .catch((reason) => Promise.reject(reason))
                    ;

                    download(JSON.stringify(response, null, 2), `${id}.encrypted.json`, 'application/json');
                }
            } catch (error) {
                alert(error);
            } finally {
                event.target.parentNode.querySelectorAll('button').forEach((button) => button.disabled = false);
            }
        })();

        return false;
    })
});
