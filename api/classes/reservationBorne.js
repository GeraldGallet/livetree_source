/*********** RESERVATION_BORNE **********/
/* Part that touches the personal cars of the users */

class ReservationBorne {
  constructor() { }

  // Gets all the personal car from a user
  get_all(body, res) {
    var query = "SELECT * FROM resa_borne;";
    //console.log(query)
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get(body, res) {
    var query = "SELECT * FROM resa_borne WHERE id_place = \'" + body.id_place + "\' AND date_resa = \'" + body.date_resa + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get_by_place(body, res) {
    var query = "SELECT * FROM resa_borne WHERE id_place = \'" + body.id_place + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Get a specified personal car
  get_by_id(body, res) {
    var query = "SELECT * FROM resa_borne WHERE id_resa = \'" + body.id_resa + "\';";
    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

  // Adds a new personal car to the specified user
  add(body, res) {
    console.log(body);
    res.locals.connection.query('INSERT INTO resa_borne SET ?', body, function (error, results, fields) {
      if (error) throw error;
      res.end(JSON.stringify(results));
    });
  }

  // Removes a specified personal car
  delete(body, res) {
    var query = 'DELETE FROM resa_borne WHERE id_resa = \'' + body.id_resa + '\'';

    res.locals.connection.query(query, function(error, results, fields) {
      if(error) throw error;
      res.send(JSON.stringify({"status": 200, "error": null, "response": results}));
    });
  }

}

module.exports = ReservationBorne;
