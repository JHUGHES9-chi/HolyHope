import wixStoresBackend from 'wix-stores-backend';

export function getProductVariants(productId) {
  return wixStoresBackend.getProductVariants(productId)
}

export function addProductMedia(productId, mediaData) {
  return wixStoresBackend.addProductMedia(productId, mediaData);
}

export function removeProductMedia(productId, media) {
  return wixStoresBackend.removeProductMedia(productId, media);
}

