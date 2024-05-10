import Axios from 'axios';

export default class DeliveryDeadlineService {
    static apiResourceEndpoint = '/api/deliveryDeadline';

    static async getAll() {
        return Axios.get(this.apiResourceEndpoint + '/getAll');
    }
}
