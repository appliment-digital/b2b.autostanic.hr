import Axios from 'axios';

export default class UserService {
    apiResourceEndpoint = '/api/user';

    async login(email, password) {
        return Axios.post('/api/login', {
            email: email,
            password: password,
        })
            .then((response) => {
                //set token in local storage that will be used for authentication with each page refresh and AJAX call
                localStorage.setItem('token', response.data.data.token);

                return response.data;
            })
            .catch((error) => {
                console.log(error.response);
                //  this.showMsg = "Incorect email or password.Please try again.";
            });
    }

    async logout() {
        return Axios.post('/api/logout', {
            token: localStorage.getItem('token'),
        })

            .then((response) => {
                return response;
            })
            .catch((error) => {
                console.log(error.response);
            });
    }

    async getAll(object) {
        return Axios.post(this.apiResourceEndpoint + '/getAll', object);
    }

    async add(object) {
        return Axios.post(this.apiResourceEndpoint + '/add', object);
    }

    async update(object) {
        return Axios.post(this.apiResourceEndpoint + '/update', object);
    }

    async deactivate(object) {
        return Axios.post(this.apiResourceEndpoint + '/deactivate', object);
    }

    async getCurrentUserData() {
        return Axios.post(this.apiResourceEndpoint + '/getCurrentUserData');
    }
}
