/*********** HAS_DOMAIN **********/
/* Part that touches the domains and the facilities that are related */

class HasDomain {
  constructor() { }

  getAll(res) {
    var query = 'SELECT * FROM has_domain;';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get the line of a specified user
  get(body, res) {
    var query = 'SELECT * FROM has_domain WHERE id_domain = \'' + body.id_domain + '\';';
    var ret = '';
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new user in the database
  add(body, res) {
    res.locals.connection.query('INSERT INTO has_domain SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Deletes a user of the database
  delete(body, res) {
    var query = 'DELETE FROM has_domain WHERE id_domain = \'' + body.id_domain + '\' AND id_facility = \'' + body.id_facility + '\';';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }
}

module.exports = HasDomain;
