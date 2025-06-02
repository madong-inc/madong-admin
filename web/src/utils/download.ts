import { VxeUI } from "vxe-pc-ui"


/**
 * 导出excel
 * @param body 
 * @param api 
 */
export const exportExcelApi = (body: any,api: { export: (arg0: any) => Promise<any> }) => {
    api.export(body).then(data => {
     if (data?.url) {
       VxeUI.modal.message({
         content: '导出成功，开始下载',
         status: 'success'
       })
       location.href = data?.url
     }
   }).catch((e) => {
     VxeUI.modal.message({
       content: '导出失败！',
       status: 'error'
     })
   })
 }
 