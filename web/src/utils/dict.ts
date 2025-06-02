import { SystemDictApi} from '#/api/system/dict';
import { useDictStore } from '#/store/modules/dict';

/**
 * 抽取公共逻辑的基础方法
 * @param dictName 字典名称
 * @param dataGetter 获取字典数据的函数
 * @param formatNumber 是否格式化字典value为number类型
 * @returns 数据
 */
function fetchAndCacheDictData<T>(
  dictName: string,
  dataGetter: () => T[],
  formatNumber = false,
): T[] {
  const { dictRequestCache, setDictInfo } = useDictStore();
  // 有调用方决定如何获取数据
  const dictItem = dataGetter();

  // 检查请求状态缓存
  if (dictItem.length === 0 && !dictRequestCache.has(dictName)) {
    const systemDictApi= new SystemDictApi();
    dictRequestCache.set(
      dictName,
      systemDictApi.getByDictType({dict_type:dictName})
        .then((result) => {
          // 缓存到store 这样就不用重复获取了
          // 内部处理了push的逻辑 这里不用push
          setDictInfo(dictName, result, formatNumber);
        })
        .catch(() => {
          // 401时 移除字典缓存 下次登录重新获取
          dictRequestCache.delete(dictName);
        })
        .finally(() => {
          // 移除请求状态缓存
          /**
           * 这里主要判断字典item为空的情况(无奈兼容 不给字典item本来就是错误用法)
           * 会导致if一直进入逻辑导致接口无限刷新
           * 在这里dictList为空时 不删除缓存
           */
          if (dictItem.length > 0) {
            dictRequestCache.delete(dictName);
          }
        }),
    );
  }
  return dictItem;
}


/**
 * 一般是Select, Radio, Checkbox等组件使用
 * @param dictName 字典名称
 * @param formatNumber 是否格式化字典value为number类型
 * @returns Options数组
 */
export function getDictOptions(dictName: string, formatNumber = false) {
  const { getDictOptions } = useDictStore();
  return fetchAndCacheDictData(
    dictName,
    () => getDictOptions(dictName),
    formatNumber,
  );
}
