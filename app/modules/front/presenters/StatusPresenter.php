<?php

namespace App\Modules\Front;

use App\Model\ORM\Addon\Addon;
use App\Model\ORM\Addon\AddonRepository;
use App\Model\WebServices\Github\Service;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\DateTime;

final class StatusPresenter extends BasePresenter
{

    /** @var AddonRepository @inject */
    public $addonRepository;

    /** @var Service @inject */
    public $github;

    /** @var Cache */
    private $cache;

    /**
     * @param IStorage $storage
     */
    public function injectCacheStorage(IStorage $storage)
    {
        $this->cache = new Cache($storage, 'Status.Page');
    }

    public function renderDefault()
    {
        $status = $this->cache->load('data', function (&$dependencies) {
            $dependencies[Cache::EXPIRE] = new DateTime('+30 minutes');
            $dependencies[Cache::TAGS] = ['status', 'status.page'];

            $status = [];

            // Build addons status ===========================================
            $status['addons']['active']['composer'] = $this->addonRepository->findBy(['state' => Addon::STATE_ACTIVE, 'type' => Addon::TYPE_COMPOSER])->countStored();
            $status['addons']['active']['bower'] = $this->addonRepository->findBy(['state' => Addon::STATE_ACTIVE, 'type' => Addon::TYPE_BOWER])->countStored();
            $status['addons']['active']['untype'] = $this->addonRepository->findBy(['state' => Addon::STATE_ACTIVE, 'type' => Addon::TYPE_UNTYPE])->countStored();
            $status['addons']['active']['unknown'] = $this->addonRepository->findBy(['state' => Addon::STATE_ACTIVE, 'type' => Addon::TYPE_UNKNOWN])->countStored();
            $status['addons']['active']['total'] = $this->addonRepository->findBy(['state' => Addon::STATE_ACTIVE])->countStored();

            $status['addons']['queued']['composer'] = $this->addonRepository->findBy(['state' => Addon::STATE_QUEUED, 'type' => Addon::TYPE_COMPOSER])->countStored();
            $status['addons']['queued']['bower'] = $this->addonRepository->findBy(['state' => Addon::STATE_QUEUED, 'type' => Addon::TYPE_BOWER])->countStored();
            $status['addons']['queued']['untype'] = $this->addonRepository->findBy(['state' => Addon::STATE_QUEUED, 'type' => Addon::TYPE_UNTYPE])->countStored();
            $status['addons']['queued']['unknown'] = $this->addonRepository->findBy(['state' => Addon::STATE_QUEUED, 'type' => Addon::TYPE_UNKNOWN])->countStored();
            $status['addons']['queued']['total'] = $this->addonRepository->findBy(['state' => Addon::STATE_QUEUED])->countStored();

            $status['addons']['archived']['composer'] = $this->addonRepository->findBy(['state' => Addon::STATE_ARCHIVED, 'type' => Addon::TYPE_COMPOSER])->countStored();
            $status['addons']['archived']['bower'] = $this->addonRepository->findBy(['state' => Addon::STATE_ARCHIVED, 'type' => Addon::TYPE_BOWER])->countStored();
            $status['addons']['archived']['untype'] = $this->addonRepository->findBy(['state' => Addon::STATE_ARCHIVED, 'type' => Addon::TYPE_UNTYPE])->countStored();
            $status['addons']['archived']['unknown'] = $this->addonRepository->findBy(['state' => Addon::STATE_ARCHIVED, 'type' => Addon::TYPE_UNKNOWN])->countStored();
            $status['addons']['archived']['total'] = $this->addonRepository->findBy(['state' => Addon::STATE_ARCHIVED])->countStored();

            $status['addons']['total']['composer'] = $this->addonRepository->findBy(['type' => Addon::TYPE_COMPOSER])->countStored();
            $status['addons']['total']['bower'] = $this->addonRepository->findBy(['type' => Addon::TYPE_BOWER])->countStored();
            $status['addons']['total']['untype'] = $this->addonRepository->findBy(['type' => Addon::TYPE_UNTYPE])->countStored();
            $status['addons']['total']['unknown'] = $this->addonRepository->findBy(['type' => Addon::TYPE_UNKNOWN])->countStored();
            $status['addons']['total']['total'] = $this->addonRepository->findAll()->countStored();

            // Build github status =============================================

            if (($response = $this->github->limit())) {
                $status['github']['core']['limit'] = $response['resources']['core']['limit'];
                $status['github']['core']['remaining'] = $response['resources']['core']['remaining'];
                $status['github']['core']['reset'] = DateTime::from($response['resources']['core']['reset']);

                $status['github']['search']['limit'] = $response['resources']['search']['limit'];
                $status['github']['search']['remaining'] = $response['resources']['search']['remaining'];
                $status['github']['search']['reset'] = DateTime::from($response['resources']['search']['reset']);
            }

            return $status;
        });


        // Fill template
        $this->template->status = $status;
    }
}
