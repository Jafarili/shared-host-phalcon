<?php


namespace Phalcon\Acl;



/***
 * Phalcon\Acl\AdapterInterface
 *
 * Interface for Phalcon\Acl adapters
 **/

interface AdapterInterface {

    /***
	 * Sets the default access level (Phalcon\Acl::ALLOW or Phalcon\Acl::DENY)
	 **/
    public function setDefaultAction($defaultAccess ); 

    /***
	 * Returns the default ACL access level
	 **/
    public function getDefaultAction(); 

    /***
	 * Sets the default access level (Phalcon\Acl::ALLOW or Phalcon\Acl::DENY)
	 * for no arguments provided in isAllowed action if there exists func for accessKey
	 **/
    public function setNoArgumentsDefaultAction($defaultAccess ); 

    /***
	 * Returns the default ACL access level for no arguments provided in
	  *isAllowed action if there exists func for accessKey
	 **/
    public function getNoArgumentsDefaultAction(); 

    /***
	 * Adds a role to the ACL list. Second parameter lets to inherit access data from other existing role
	 **/
    public function addRole($role , $accessInherits  = null ); 

    /***
	 * Do a role inherit from another existing role
	 **/
    public function addInherit($roleName , $roleToInherit ); 

    /***
	 * Check whether role exist in the roles list
	 **/
    public function isRole($roleName ); 

    /***
	 * Check whether resource exist in the resources list
	 **/
    public function isResource($resourceName ); 

    /***
	 * Adds a resource to the ACL list
	 *
	 * Access names can be a particular action, by example
	 * search, update, delete, etc or a list of them
	 **/
    public function addResource($resourceObject , $accessList ); 

    /***
	 * Adds access to resources
	 **/
    public function addResourceAccess($resourceName , $accessList ); 

    /***
	 * Removes an access from a resource
	 **/
    public function dropResourceAccess($resourceName , $accessList ); 

    /***
	 * Allow access to a role on a resource
	 **/
    public function allow($roleName , $resourceName , $access , $func  = null ); 

    /***
	 * Deny access to a role on a resource
	 **/
    public function deny($roleName , $resourceName , $access , $func  = null ); 

    /***
	 * Check whether a role is allowed to access an action from a resource
	 **/
    public function isAllowed($roleName , $resourceName , $access , $parameters  = null ); 

    /***
	 * Returns the role which the list is checking if it's allowed to certain resource/access
	 **/
    public function getActiveRole(); 

    /***
	 * Returns the resource which the list is checking if some role can access it
	 **/
    public function getActiveResource(); 

    /***
	 * Returns the access which the list is checking if some role can access it
	 **/
    public function getActiveAccess(); 

    /***
	 * Return an array with every role registered in the list
	 **/
    public function getRoles(); 

    /***
	 * Return an array with every resource registered in the list
	 **/
    public function getResources(); 

}