import Axios from "axios";

export default class UserService {
    apiResourceEndpoint = '/api/user';

    async login(email, password) {
        return Axios.post("/api/login", {
            email: email,
            password: password,
        })
            .then((response) => {
                //set token in local storage that will be used for authentication with each page refresh and AJAX call
                localStorage.setItem("token", response.data.data.token);

                return response.data;
            })
            .catch((error) => {
                console.log(error.response);
                //  this.showMsg = "Incorect email or password.Please try again.";
            });
    }

    async add(object) {
        return Axios.post(this.apiResourceEndpoint + "/add", object);
    }
}
