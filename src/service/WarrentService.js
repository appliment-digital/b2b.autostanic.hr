import Axios from 'axios';

export default class WarrentService {
    static apiResourceEndpoint = '/api/warrent';

    static async getAll() {
        return Axios.get(this.apiResourceEndpoint + '/getAll');
    }
}
