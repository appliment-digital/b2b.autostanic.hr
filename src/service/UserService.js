import Axios from 'axios';

export default class UserService {
    apiResourceEndpoint = '/api/user';
    static apiResourceEndpoint = '/api/user';

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

    async forgotPassword(email) {
        return Axios.post('/api/forgot-password', email);
    }

    async resetPassword(object) {
        return Axios.post('/api/reset-password', object);
    }

    async getAll() {
        return Axios.post(this.apiResourceEndpoint + '/getAll');
    }

    async getAllWithRelations() {
        return Axios.post(this.apiResourceEndpoint + '/getAllWithRelations');
    }

    async add(object) {
        return Axios.post(this.apiResourceEndpoint + '/add', object);
    }

    async update(object) {
        return Axios.put(
            this.apiResourceEndpoint + '/update/' + object.id,
            object,
        );
    }

    async changeStatus(object) {
        return Axios.post(this.apiResourceEndpoint + '/changeStatus', object);
    }

    static async getCurrentUserData() {
        return Axios.get(this.apiResourceEndpoint + '/getCurrentUserData');
    }

    async getRoles() {
        return Axios.get(this.apiResourceEndpoint + '/getRoles');
    }
}
