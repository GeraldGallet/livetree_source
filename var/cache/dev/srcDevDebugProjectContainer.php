<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerQsxidA7\srcDevDebugProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerQsxidA7/srcDevDebugProjectContainer.php') {
    touch(__DIR__.'/ContainerQsxidA7.legacy');

    return;
}

if (!\class_exists(srcDevDebugProjectContainer::class, false)) {
    \class_alias(\ContainerQsxidA7\srcDevDebugProjectContainer::class, srcDevDebugProjectContainer::class, false);
}

return new \ContainerQsxidA7\srcDevDebugProjectContainer(array(
    'container.build_hash' => 'QsxidA7',
    'container.build_id' => '10ad65cb',
    'container.build_time' => 1522997027,
), __DIR__.\DIRECTORY_SEPARATOR.'ContainerQsxidA7');