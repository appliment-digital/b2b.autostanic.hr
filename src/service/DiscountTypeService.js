import Axios from 'axios';

export default class DiscountTypeService {
    apiResourceEndpoint = '/api/discountType';

    async getAll() {
        return Axios.get(this.apiResourceEndpoint + '/getAll');
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

    async delete(id) {
        return Axios.delete(this.apiResourceEndpoint + '/delete/' + id);
    }
}
