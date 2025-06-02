
import type { App, Component } from 'vue';



/**
 * -转大驼峰
 * @param str
 */
export const toPascalCase = (str: any) => {
  // 将连字符或下划线替换为空格，以便后续处理
  const words = str.replaceAll(/[-_]/g, ' ').split(' ');

  // 将每个单词的首字母大写，并将其余部分保持原样
  const pascalCaseWords = words.map((word: any) => {
    if (word) {
      return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
    }
    return word;
  });

  // 将处理后的单词拼接成一个新的字符串
  return pascalCaseWords.join('');
};


/**
 * 转-kebab
 */
export const toKebabCase = (str: string) => {
  // 将每个单词的首字母小写，并在单词之间插入连字符
  return str
    .replaceAll(/([A-Z])/g, (match, _p1, offset) => {
      // 如果是第一个字符，直接转为小写
      if (offset === 0) {
        return match.toLowerCase();
      }
      // 否则，在大写字母前加上连字符，并将大写字母转为小写
      return `-${match.toLowerCase()}`;
    })
    .toLowerCase(); // 确保整个字符串都是小写
};


type EventShim = {
  new (...args: any[]): {
    $props: {
      onClick?: (...args: any[]) => void;
    };
  };
};

export type WithInstall<T> = T & {
  install(app: App): void;
} & EventShim;

export type CustomComponent = Component & { displayName?: string };
export const withInstall = <T extends CustomComponent>(component: T, alias?: string) => {
  (component as Record<string, unknown>).install = (app: App) => {
    const compName = component.name || component.displayName;
    if (!compName) return;
    app.component(compName, component);
    if (alias) {
      app.config.globalProperties[alias] = component;
    }
  };
  return component as WithInstall<T>;
};
