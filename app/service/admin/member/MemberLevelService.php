<?php
declare(strict_types=1);

namespace app\service\admin\member;

use app\dao\member\MemberDao;
use app\dao\member\MemberLevelDao;
use core\base\BaseService;
use core\exception\handler\AdminException;

/**
 * 会员等级服务类
 */
class MemberLevelService extends BaseService
{

    /**
     * 构造方法
     */
    public function __construct(MemberLevelDao $dao)
    {
        $this->dao = $dao;
    }

}