<?php

namespace MediaWiki\Skins\Mirage\ResourceLoader;

use CSSMin;
use InvalidArgumentException;
use MediaWiki\Skins\Mirage\Avatars\AvatarLookup;
use MediaWiki\Skins\Mirage\Avatars\NullAvatarLookup;
use ResourceLoaderContext;
use ResourceLoaderFileModule;

class MirageAvatarResourceLoaderModule extends ResourceLoaderFileModule {
	/** @var AvatarLookup */
	private $avatarLookup;

	/**
	 * @param array $options
	 * @param string|null $localBasePath
	 * @param string|null $remoteBasePath
	 * @param AvatarLookup $avatarLookup
	 */
	public function __construct(
		array $options,
		?string $localBasePath,
		?string $remoteBasePath,
		AvatarLookup $avatarLookup
	) {
		if ( $avatarLookup instanceof NullAvatarLookup ) {
			throw new InvalidArgumentException(
				'This ResourceLoader module can only be instantiated ' .
				'with an actual avatar lookup, not NullAvatarLookup!'
			);
		}

		parent::__construct( $options, $localBasePath, $remoteBasePath );

		$this->avatarLookup = $avatarLookup;
	}

	/**
	 * @inheritDoc
	 */
	public function getStyles( ResourceLoaderContext $context ) : array {
		$styles = parent::getStyles( $context );

		$avatarUrl = CSSMin::buildUrlValue(
			$this->avatarLookup->getAvatarForUser( $context->getUserObj() )
		);

		$screenStyles = $styles['screen'] ?? '';
		$screenStyles .= <<<CSS
.skin-mirage-avatar-holder:before { background-image: $avatarUrl; }
CSS;
		$styles['screen'] = $screenStyles;

		return $styles;
	}
}
