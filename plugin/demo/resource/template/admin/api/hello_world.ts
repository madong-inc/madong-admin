import BaseService from '@/api/core/base';



import request from '@/utils/http';

const baseUrl = '/test';

export const TestService = {
    ...BaseService<any>({ baseUrl }),
}