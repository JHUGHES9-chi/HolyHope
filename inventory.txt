import wixStoresBackend from 'wix-stores-backend';

export function incrementInventory(incrementInfo) {
  return wixStoresBackend.incrementInventory(incrementInfo);
}

export function decrementInventory(decrementInfo) {
  return wixStoresBackend.decrementInventory(decrementInfo);
}
