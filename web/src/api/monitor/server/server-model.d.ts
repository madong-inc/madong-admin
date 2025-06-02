// 系统监控数据类型
export interface ServerInfo {
  cpu: CpuInfo;
  memory: MemoryInfo;
  disk: DiskInfo[];
  php: PhpInfo;
}
 
// CPU 信息类型
export interface CpuInfo {
  cpu_name: string;
  physical_cores: string; // 物理核心数（字符串类型）
  logical_cores: string;  // 逻辑核心数（字符串类型）
  cache_size_mb: number;  // 缓存大小（MB，数值类型）
  cpu_usage_percentage: number; // CPU 使用率（百分比）
  free_cpu_percentage: number;  // 空闲 CPU 百分比
}
 
// 内存信息类型
export interface MemoryInfo {
  total_memory: number;         // 总内存（GB，数值类型）
  available_memory: number;     // 可用内存（GB，数值类型）
  used_memory: number;          // 已用内存（GB，数值类型）
  php_memory_usage: number;     // PHP 内存使用率（百分比）
  memory_usage_rate: string;    // 内存使用率（原始字符串格式）
}
 
// 磁盘信息类型（数组项）
export interface DiskInfo {
  filesystem: string;           // 文件系统标识
  size: string;                 // 磁盘总大小（含单位）
  available: string;            // 剩余空间（含单位）
  used: string;                 // 已用空间（含单位）
  use_percentage: string;       // 使用率百分比（含%符号）
  mounted_on: string;           // 挂载点
}
 
// PHP 配置信息类型
export interface PhpInfo {
  php_version: string;                // PHP 版本
  os: string;                         // 操作系统类型
  project_path: string;               // 项目路径
  memory_limit: string;               // 内存限制
  max_execution_time: string;         // 最大执行时间
  error_reporting: string;            // 错误报告级别
  display_errors: "on" | "off";       // 错误显示开关（严格类型）
  upload_max_filesize: string;        // 最大上传文件大小
  post_max_size: string;              // POST 数据最大大小
  extension_dir: string;              // 扩展目录
  loaded_extensions: string;          // 已加载扩展（逗号分隔）
}