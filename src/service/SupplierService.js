import Axios from 'axios';

export default class SupplierDetailService {
    apiResourceEndpoint = '/api/bitrix';

    async getAll() {
        return Axios.post(this.apiResourceEndpoint + '/getAll/');
    }

    async getAllCategoriesForSupplier(id) {
        return Axios.post(
            this.apiResourceEndpoint + '/getAllCategoriesForSupplier/' + id,
        );
    }
}
