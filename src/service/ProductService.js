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
}
