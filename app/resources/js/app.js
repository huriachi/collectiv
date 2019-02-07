try {
    window.$ = require('jquery');
    require('bootstrap');
} catch {
    console.error('Could not load Bootstrap!');
}

import {User} from "./classes";

window.user = new User();