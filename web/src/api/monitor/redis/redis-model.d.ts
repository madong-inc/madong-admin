export interface RedisProps {
    description?: string;
    name?: string;
    title?: string;
  }
  
export interface DescriptionItem {
    content: Component | string;
    title: string;
}


// 主数据类型
export interface RedisMonitorData {
  uptime_in_seconds: number;
  connected_clients: number;
  used_memory: string; // 保持原始格式（如："686.79 KB"）
  memory_fragmentation_ratio: number;
  total_commands_processed: number;
  total_connections_received: number;
  keyspace_hits: number;
  keyspace_misses: number;
  hit_rate: number;
  variable: RedisVariableInfo;
  data: RedisDataEntries;
}
 
// 变量信息类型（嵌套对象）
export interface RedisVariableInfo {
  redis_version: string;
  redis_git_sha1: number | string; // 原始数据中包含数字0和字符串类型
  redis_git_dirty: number;
  redis_build_id: string;
  redis_mode: "standalone" | "cluster"; // 根据实际值限定
  os: string;
  arch_bits: 64 | 32; // 根据实际值限定
  multiplexing_api: "WinSock_IOCP" | string; // 根据实际值扩展
  process_id: number;
  run_id: string;
  tcp_port: number;
  uptime_in_seconds: number;
  uptime_in_days: number;
  hz: number;
  lru_clock: number;
  config_file: string;
  // 以下字段根据实际数据补充完整类型
  client_longest_output_list: number;
  client_biggest_input_buf: number;
  blocked_clients: number;
  used_memory: number; // 原始数值（与顶层used_memory格式不同）
  used_memory_human: string;
  used_memory_rss: number;
  used_memory_peak: number;
  used_memory_peak_human: string;
  used_memory_lua: number;
  mem_fragmentation_ratio: number;
  mem_allocator: string;

}
 
// 数据存储类型（键值对集合）
interface RedisDataEntries {
  [key: string]: string; // 所有值保持原始序列化字符串格式
}