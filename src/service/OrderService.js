import Axios from 'axios';

export default class OrderService {
    apiResourceEndpoint = '/api/order';

    async createOrder(object) {
        return Axios.post(this.apiResourceEndpoint + '/createOrder', object);
    }
}
