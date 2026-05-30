// Configura axios global para peticiones AJAX del frontend.
import axios from 'axios';
window.axios = axios;

// Marca peticiones como XHR para middleware/controladores que lo requieren.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
