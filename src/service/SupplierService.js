import Axios from 'axios';

export default class SupplierDetailService {
    apiResourceEndpoint = '/api/supplier';

    async getAll() {
        return Axios.post(this.apiResourceEndpoint + '/getAll');
    }

    async getCategoriesForSupplier(id) {
        return Axios.get(
            this.apiResourceEndpoint + '/getCategoriesForSupplier/' + id,
        );
    }
}
