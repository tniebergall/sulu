<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\CustomUrlBundle;

use PHPCR\Migrations\VersionInterface;
use PHPCR\SessionInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version201904110902 implements VersionInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(SessionInterface $session)
    {
        $liveSession = $this->container->get('sulu_document_manager.live_session');
        $this->upgrade($liveSession);
        $this->upgrade($session);

        $liveSession->save();
        $session->save();
    }

    public function down(SessionInterface $session)
    {
    }

    private function upgrade(SessionInterface $session)
    {
        $queryManager = $session->getWorkspace()->getQueryManager();

        $query = 'SELECT * FROM [nt:unstructured] WHERE [jcr:mixinTypes] = "sulu:custom_url"';
        $rows = $queryManager->createQuery($query, 'JCR-SQL2')->execute();

        foreach ($rows as $row) {
            $node = $row->getNode();

            $property = $node->getProperty('domainParts');
            $oldDomainParts = \json_decode($property->getValue('domainParts'), true);
            $newDomainParts = [];

            if ($oldDomainParts['prefix']) {
                $newDomainParts[] = $oldDomainParts['prefix'];
            }

            $newDomainParts = \array_merge($newDomainParts, $oldDomainParts['suffix']);

            $property->remove('domainParts');
            $node->setProperty('domainParts', \json_encode($newDomainParts));
        }
    }
}
