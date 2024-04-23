import Axios from 'axios';

export default class WebDatabaseService {
    apiResourceEndpoint = '/api/';

    async test() {
        return Axios.get(this.apiResourceEndpoint + '/test');
    }
}
