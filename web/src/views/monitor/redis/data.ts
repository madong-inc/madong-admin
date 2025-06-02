import { $t } from "#/locale";

/**
 * 字段标题
 * @returns 
 */
export function labelMapping(){
  return {
    redis_version: $t('monitors.redis.columns.redis_version'),
    redis_mode: $t('monitors.redis.columns.redis_mode'),
    os: $t('monitors.redis.columns.os'),
    arch_bits: $t('monitors.redis.columns.arch_bits'),
    multiplexing_api: $t('monitors.redis.columns.multiplexing_api'),
    process_id: $t('monitors.redis.columns.process_id'),
    run_id: $t('monitors.redis.columns.run_id'),
    tcp_port: $t('monitors.redis.columns.tcp_port'),
    uptime_in_days: $t('monitors.redis.columns.uptime_in_days'),
    role: $t('monitors.redis.columns.role'),
    connected_clients: $t('monitors.redis.columns.connected_clients'),
    used_memory_human: $t('monitors.redis.columns.used_memory_human'),
    used_memory_peak_human: $t('monitors.redis.columns.used_memory_peak_human'),
    mem_allocator: $t('monitors.redis.columns.mem_allocator'),
    total_connections_received: $t('monitors.redis.columns.total_connections_received'),
    total_commands_processed: $t('monitors.redis.columns.total_commands_processed'),
    instantaneous_ops_per_sec: $t('monitors.redis.columns.instantaneous_ops_per_sec'),
    total_net_input_bytes: $t('monitors.redis.columns.total_net_input_bytes'),
    instantaneous_input_kbps: $t('monitors.redis.columns.instantaneous_input_kbps'),
    instantaneous_output_kbps: $t('monitors.redis.columns.instantaneous_output_kbps'),
    rejected_connections: $t('monitors.redis.columns.rejected_connections'),
    keyspace_hits: $t('monitors.redis.columns.keyspace_hits'),
    keyspace_misses: $t('monitors.redis.columns.keyspace_misses'),
    config_file: $t('monitors.redis.columns.config_file'),
  };
}