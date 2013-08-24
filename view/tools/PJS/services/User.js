/**
 * @namespace PPHP\view\tools\PJS\services\User
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.services.User', function(Y){
  /**
   * Данная служба хранит информацию о текущем пользователе.
   */
  var User = (function(){
    /**
     * Идентификатор текущего пользователя или null если пользователь не аутентифицирован.
     * @private
     * @static
     * @type {integer|null}
     */
    var OID = null,
      /**
       * Массив ролей, делегированных текущему пользователю.
       * Роли включают следующие свойства:
       * - OID - идентификатор роли;
       * - name - имя роли.
       * @private
       * @static
       * @type {Object[]}
       */
      roles = [];

    Y.PJS.services.Query.query('SystemPackages', 'hasModule', {
      data: ['Users'],
      success: function(answer){
        if(answer){
          Y.PJS.services.Query.query('Users', 'identifyUser', {
            success: function(answer){
              OID = answer.OID;
              Y.PJS.services.Query.query('SystemPackages', 'hasModule', {
                data: ['Access'],
                success: function(answer){
                  if(answer){
                    Y.PJS.services.Query.query('Access', 'getRolesUser', {
                      success: function(answer){
                        roles = answer;
                        User.fire('PJS.services.User:initializer');
                      }
                    });
                  }
                  else{
                    User.fire('PJS.services.User:initializer');
                  }
                }
              })
            }
          });
        }
        else{
          User.fire('PJS.services.User:initializer');
        }
      }
    });

    return {
      /**
       * Метод возвращает идентификатор текущего пользователя.
       * @public
       * @function
       * @return {integer|null} Идентификатор текущего пользователя или null если пользователь не идентифицирован.
       */
      getOID: function(){
        return OID;
      },

      /**
       * Метод возвращает массив ролей, делегированных текущему пользователю.
       * @public
       * @function
       * @return {Object[]} Массив ролей, делегированных текущему пользователю или пустой массив, если пользователю не делегировано ни одной роли.
       */
      getRoles: function(){
        return roles;
      },

      /**
       * Метод определяет, делегирована ли некоторая роль текущему пользователю.
       * @public
       * @function
       * @param {string} role Имя целевой роли.
       * @return {boolean} true - если роль делегирована, иначе - false.
       */
      hasRole: function(role){
        for(var i in roles){
          if(roles[i].name == role){
            return true;
          }
        }
        return false;
      }
    }
  })();

  Y.augment(User, Y.EventTarget);
  Y.namespace('PJS.services').User = User;
}, '1.0', {requires: ['PJS.services.Query', 'event', 'oop']});