/*************************
 backend/http-functions.js
 *************************

 'backend/http-functions.js' is a reserved Velo file that lets you expose APIs that respond to fetch requests from external services.

 In this file you create APIs to expose the functionality of your site as a service. That is, other people can use 
 the functionality of your site by writing code that calls your site's APIs, as defined by the functions you create here.

 Using the provided code (below this comment block) as an example, users of your HTTP functions can call your API using the following patterns: 

 Production endpoints:
  • Premium site:
    https://mysite.com/_functions/multiply?leftOperand=3&rightOperand=4
  • Free sites:
    https://username.wixsite.com/mysite/_functions/multiply?leftOperand=3&rightOperand=4

 Test endpoints:
  • Premium sites:
    https://mysite.com/_functions-dev/multiply?leftOperand=3&rightOperand=4
  • Free sites:
    https://username.wixsite.com/mysite/_functions-dev/multiply?leftOperand=3&rightOperand=4

 ---
 About HTTP functions: 
 https://support.wix.com/en/article/velo-exposing-a-site-api-with-http-functions

 API Reference:
 https://www.wix.com/velo/reference/wix-http-functions

**********************/

// The following is an example of an HTTP function, which gets the product of 2 operands. Adapt the code below for your specific use case.

import {created, ok, badRequest, notFound, serverError} from 'wix-http-functions';
import wixData from 'wix-data';

import wixStoresBackend from 'wix-stores-backend';
import { getProductVariants, addProductMedia, removeProductMedia } from 'backend/products';
import { incrementInventory, decrementInventory } from 'backend/inventory';

import { mediaManager } from 'wix-media-backend';

export async function getFileUrl() {
  return mediaManager.getFileUrl("wix:image://v1/97d5d5_166063b3f8294232a61099ff7448ee0c~mv2.jpg/file.jpg#originWidth=2525&originHeight=3000");
}


/**
* deleteProduct function
*
* This is a USE request
* 
* This function expects to be called with 1 parameter(productID) for example:
* https://mysite.com/_functions/deleteProduct/productID
* 
*/

export async function use_deleteProduct(request){
  let options = {
    "headers": {
      "Content-Type": "application/json"
    }
  };
    if( verify_connection(request.path[0])){
    console.log("Good login")
  }  else{
    return badRequest(options);
  }
    let productId = request.path[1]
    wixStoresBackend.deleteProduct(productId)
    .then( () => {
      // product has been deleted
    })
    .catch( (err) => {
      console.log(err)
    });
}


/**
 * Deletes media from product or event
 */
function del_media(productId){
  return removeProductMedia(productId)
}

function add_media(productId, src){
  //let productId = "a8ddae56-88ea-5001-555f-746b4b49e938"
  //let src = "https://i0.wp.com/www.14again.net/wp-content/uploads/2021/09/lucky.jpg"
  let mediaData = [{ // add media item to the product
    src
  }]

  return addProductMedia(productId, mediaData)
}

/**
* Authentication function to verify a secretphrase password
*/
function verify_connection(psswd){
  let authenticated = false;

  var password = "secretphrase" /**Secret passphrase variable */

  if(password == psswd){
    authenticated = true
  }
  
  return authenticated;

  
}
/**
* Externally visible function 'query_quantity' that returns the quantity of products available or the remaining spaces left for an event.
*/
export async function query_quantity(productId){
  var inventory = 0
  let quantityQuery = await wixData.query("Stores/Products")
    .eq('_id', productId)
    .find()
    .then ( (results) =>{
       console.log(results)
    })
    .catch ( (err) => {
        let errorMsg = err
    });
  return inventory

  
}

/**
* Multi threading function to decrement the stock level of a product/event
*/

async function decrementHandler(productId, value) {
  value = value * -1
  let variants = await getProductVariants(productId);

  decrementInventory(
    [{
      variantId: variants[0]._id,
      productId: productId,
      decrementBy: value
    }])
    .then(() => {
      console.log("Inventory decremented successfully")
    })
    .catch(error => {
      // Inventory decrement failed
      console.error(error);
    })
}


/**
* Multi threading function to increment the stock level of a product/event
*/

async function incrementHandler(productId, value) {

  let variants = await getProductVariants(productId);
  //console.log(variants)

  incrementInventory(
    [{
      variantId: variants[0]._id,
      productId: productId,
      incrementBy: value
    }])
    .then(() => {
      console.log("Inventory incremented successfully")
    })
    .catch(error => {
      // Inventory increment failed
      console.error(error);
    })
}

export async function use_updateEvent(request){

  await console.log(request)
  var ip = "78.151.81.119/HolyHope/images/"

  
  let options = {
    "headers": {
      "Content-Type": "application/json"
    }
  };

  if( verify_connection(request.path[0])){
    console.log("Good login")
  }  else{
    return badRequest(options);
  }

  var productId = request.path[1]
  var name = parse_string(request.path[2])
  var max_attendees = request.path[3]
  var event_desc = parse_string(request.path[4])
  console.log(productId)
  //Date isn't a field that is stored. We ne
  var price = request.path[5]
  if(request.path[6] == 'null'){
      console.log("no image provided")
  }
  else{
      var image_src = ip.concat(request.path[6])
      console.log(image_src)
      add_media(productId, image_src)
  }


  wixStoresBackend.updateProductFields(productId, {
    "name": name,
    "description": event_desc,
    "price": price
  })
  .then((product) => {
    
  })
  .catch((error) => {
    // There was an error updating the product
    return badRequest
  });

  let inventory = await query_quantity(productId)

  let increment_amount = max_attendees - inventory 
  console.log("change = " + increment_amount)

  if(increment_amount > 0){
      incrementHandler(productId, increment_amount)
  }
  else if (increment_amount < 0){
      decrementHandler(productId, increment_amount)
  }
  else{
    console.log("Already correct value.")
  }

  

  return ok(options)
}

/**
 * parse_string
 *  Replaces '_' with ' ' as all information is sent over URL and ' ' would break the URL
 */

function parse_string(string){
  let new_str = string.replace(/_/ig, " ")
  console.log(new_str)
  return(new_str);
}


/**
 * Test function
 */
export function test(){
  let str = "different_name_to_test"
  console.log(parse_string(str))
}

/**
 * Exposed http API to add event to wix customer site
 * 
 * Requires parameters in this order:
 * 
 * 1 - Secret passphrase
 * 2 - Product/EventID
 * 3 - Maximum atendee's
 * 4 - event description
 * 5 - price
 * 6 - new event media (optional)
 */
export async function use_add_event(request){
  console.log(request)
  //await console.log(request.query)
  let ip = "http://78.151.81.119/HolyHope/images/"

  
  let options = {
    "headers": {
      "Content-Type": "application/json"
    }
  };


  if( verify_connection(request.path[0])){
    console.log("Good login")
  }  else{
    return badRequest(options);
  }

  var productId = request.path[1]
  var name = await parse_string(request.path[2])
  //console.log(name)
  var max_attendees = request.path[3]
  var event_desc = await parse_string(request.path[4])
  console.log(productId)
  //Date isn't a field that is stored. We ne
  var price = request.path[5]
  var image_src = ip + request.path[6]
  del_media(productId)


  wixStoresBackend.updateProductFields(productId, {
    "name": name,
    "description": event_desc,
    "price": price,
    "visible": true
  })
  .then((product) => {
    
  })
  .catch((error) => {
    // There was an error updating the product
    return badRequest
  });

  let inventory = await query_quantity(productId)

  let increment_amount = max_attendees - inventory 
  console.log("change = " + increment_amount)

  if(increment_amount > 0){
      incrementHandler(productId, increment_amount)
  }
  else if (increment_amount < 0){
      decrementHandler(productId, increment_amount)
  }
  else{
    console.log("Already correct value.")
  }

  
  add_media(productId, image_src)

  return ok()
}
/**
 * Exposed function to get all events, this is used to keep the two databases synced and was used to create the original dataset
 */
export async function get_events(request) {
  let options = {
    "headers": {
      "Content-Type": "application/json"
    }
  };


  const search_op = {

      "suppressAuth": true,

      appOptions: {

        "includeHiddenProducts": true

      }

    }
  if(verify_connection(request.query["password"])){
    console.log("Good login")
  }  else{
    return badRequest(options);
  }

  let results = await wixData.query("Stores/Products")
    .eq('productType', 'digital')
    //.isNotEmpty("title")
    .limit(100)
    .find(search_op)
  
  options.body = {
    "items": results.items
  }
  return ok(options)
  }



 export function get_items(){
   wixData.query("Stores/Products")
   .eq("productType", "digital")
  .find()
  .then( (results) => {
    console.log(results.items)
  } ) ;
  return ok;
 }
  
export function get_insert(){
  let toInsert = {
    "_id": "0000001",
    "title": "test",
    
  };

  wixData.insert("test", toInsert)
    .then ( (results) =>{
        let item = results;
        return
    })
    .catch ( (err) => {
        let errorMsg = err
        return badRequest(errorMsg)
    })
}
