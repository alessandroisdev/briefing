// Import Typescript and configure base UI
import 'bootstrap';
import axios from 'axios';

// Set global axios defaults
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

console.log('Briefing App - Frontend Initialized');
