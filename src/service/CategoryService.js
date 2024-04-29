import Axios from 'axios';

export default class CategoryService {
    static apiResourceEndpoint = '/api/category';

    static async getMainCategories() {
        return Axios.get(this.apiResourceEndpoint + '/categories');
    }

    static async getSubcategories(id) {
        return Axios.get(`${this.apiResourceEndpoint}/subcategories/${id}`);
    }
}
