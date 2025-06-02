export interface SystemFilesRow { 
    id: string;
    url: string;
    size: string;
    size_info: string;
    hash: string;
    filename: string;
    original_filename: string;
    base_path: string; 
    path: string;
    ext: string;
    content_type: string;
    platform: string;
    th_url: string; // 缩略图URL 
    th_filename: string; // 缩略图文件名 
    th_size: string; // 缩略图大小 
    th_size_info: string; // 缩略图大小信息
    th_content_type: string; // 缩略图内容类型
    object_id: string;
    object_type: string;
    attr: string; 
    created_at?: string;
    updated_at?: string;
    created_by?: string;
    updated_by?: string;
  }
