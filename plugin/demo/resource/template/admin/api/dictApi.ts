import request from '@/utils/http'

export class DictService {
  // 获取所有字典
  static getAllDict() {
    return request.post<any>({
      url: '/sys/dict/enumDictList',
      showErrorMessage: false // 不显示错误消息
    })
  }
}
