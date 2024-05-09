import Axios from 'axios';

export default class BitrixService {
    apiResourceEndpoint = '/api/bitrix';

    async getCountriesList() {
        return Axios.post(this.apiResourceEndpoint + '/getCountriesList');
    }
}
