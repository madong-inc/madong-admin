import { DictItem } from "./dict-item-model";


export interface SystemDictRow {
    id: string;
    group_code: string;
    name: string;
    code: string;
    data_type: string;
    description: string;
    enabled: number;
    sort: number;
    created_by: string;
    updated_by: string;
    updated_at: string;
}

export interface DictOptions extends DictItem {
    label: string;
    value: string | number;
}

export type ApiResponse<T> = Promise<{
    code: number;
    data: T;
    msg: string;
}>;
