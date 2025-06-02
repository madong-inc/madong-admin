import type { ConfigEnv, PluginOption, UserConfig } from 'vite';

/**
 * 压缩归档插件配置
 */
interface ArchiverPluginOptions {
  /**
   * 输出文件名
   * @default dist
   */
  name?: string;
  /**
   * 输出目录
   * @default .
   */
  outputDir?: string;
}

/**
 * 用于判断是否需要加载插件
 */
interface ConditionPlugin {
  // 判断条件
  condition?: boolean;
  // 插件对象
  plugins: () => PluginOption[] | PromiseLike<PluginOption[]>;
}

interface CommonPluginOptions {
  /** 是否开启devtools */
  devtools?: boolean;
  /** 环境变量 */
  env?: Record<string, any>;
  /** 是否构建模式 */
  isBuild?: boolean;
  /** 构建模式 */
  mode?: string;
}

interface ApplicationPluginOptions extends CommonPluginOptions {
  /** 开启后，会在打包dist同级生成dist.zip */
  archiver?: boolean;
  /** 压缩归档插件配置 */
  archiverPluginOptions?: ArchiverPluginOptions;
  /** 开启 gzip|brotli 压缩 */
  compress?: boolean;
  /** 压缩类型 */
  compressTypes?: ('brotli' | 'gzip')[];
  /** 是否开启html插件  */
  html?: boolean;
  /** 是否注入app loading */
  injectAppLoading?: boolean;
  /** 是否开启vxe-table懒加载 */
  vxeTableLazyImport?: boolean;
}

type ApplicationOptions = ApplicationPluginOptions;

type DefineApplicationOptions = (config?: ConfigEnv) => Promise<{
  application?: ApplicationOptions;
  vite?: UserConfig;
}>;

type DefineConfig = DefineApplicationOptions;

export type {
  ApplicationPluginOptions,
  ArchiverPluginOptions,
  CommonPluginOptions,
  ConditionPlugin,
  DefineApplicationOptions,
  DefineConfig,
};
