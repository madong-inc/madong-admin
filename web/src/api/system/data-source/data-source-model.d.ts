export interface SystemDataSourceRow {
    id?: string;               
    tenant_id: string;        
    db_domain: string;        
    db_host: string;          
    db_port: string;          
    db_name: string;          
    db_user: string;          
    db_password: string;       
    name: string;             
    phone: string;            
    license_number: string;   
    description: string;      
    address: string;          
    domain: string;           
    account_count: number;    
    sort: number;             
    is_system: number;        
    enabled: number;          
  }