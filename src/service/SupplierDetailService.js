import Axios from 'axios';

export default class SupplierDetailService {
    apiResourceEndpoint = '/api/bitrix';

    async getSupplierWithDetails(id) {
        return Axios.get(
            this.apiResourceEndpoint + '/getSupplierWithDetails/' + id,
        );
    }

    async getAllSuppliersWithDetails() {
        return Axios.post(this.apiResourceEndpoint + '/getSupplierWithDetails');
    }

    async addDetailsforSupplier(object) {
        return Axios.post(
            this.apiResourceEndpoint + '/addDetailsforSupplier',
            object,
        );
    }

    async updateDetailsforSupplier(id, object) {
        return Axios.put(
            this.apiResourceEndpoint + '/addDetailsforSupplier/' + id,
            object,
        );
    }
}
