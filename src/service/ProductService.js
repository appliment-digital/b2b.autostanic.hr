import Axios from 'axios';

export default class ProductService {
    static apiResourceEndpoint = '/api/product';

    static async getProductsByCategoryId(categoryId, page, pageSize) {
        return Axios.get(
            this.apiResourceEndpoint +
                '/getProductsByCategoryId/' +
                categoryId +
                '/' +
                page +
                '/' +
                pageSize,
        );
    }

    static async getProductById(id) {
        return Axios.get(this.apiResourceEndpoint + '/getProductById/' + id);
    }
}
