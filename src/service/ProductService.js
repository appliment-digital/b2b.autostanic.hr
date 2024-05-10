import Axios from 'axios';

export default class ProductService {
    static apiResourceEndpoint = '/api/product';

    static async getProductsByCategoryId(categoryId, page, pageSize, filter = {}) {
        return Axios.post(
            this.apiResourceEndpoint +
                '/getProductsByCategoryId/' +
                categoryId +
                '/' +
                page +
                '/' +
                pageSize,
            filter,
        );
    }

    static async getProductPictures(id) {
        return Axios.get(this.apiResourceEndpoint + '/getProductPictures/' + id);
    }
}
