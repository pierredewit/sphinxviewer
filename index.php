<?php
  /**
   * There is nothing special, but I was missing it
   * @author  Roman Negrulenko lifekent@gmail.com
   * @link    lifekent.com
   */

  /**
   * Connection settings
   */
  $host = '127.0.0.1';
  $port = 9306;
  
  /**
   * Bootstrapping and quering
   */
  new DB($host, $port);

  /**
   * Query class
   */
  class DB
  {
    public static $connection;

    public function __construct($host = '127.0.0.1', $port = 9306)
    {
      self::$connection = @new \MySQLi($host, null, null, null, $port, null);
      if (self::$connection->connect_error) 
        die("There was a problem with a connection: " . self::$connection->connect_error);
    }

    public static function query($q)
    {
      if ( !$result = self::$connection->query($q)) return false;
      
      $data = array();
      while($row = $result->fetch_assoc())
          $data[] = $row;

      array_walk($data, function(&$v){$v = (object)$v; });

      return $data;
    }

  }

  // Processing
  if (!empty($_POST['query']))
  {
    $metadata = new stdClass;
    $data = DB::query($_POST['query']);
    $meta = (object)DB::$connection->query('SHOW META');

    while($row = $meta->fetch_assoc())
    {
      $k = reset(array_values($row));
      $v = end(array_values($row));
      $metadata->$k = $v;
    }
   
  } 

  $indices = DB::query('SHOW TABLES');

?>
<!-- Template view -->
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SphinxViewer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
      
      body {
        padding-top: 50px;
      }

    </style>
  </head>
  <body>

    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">SphinxViewer</a>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a target="_blank" href="http://sphinxsearch.com/docs/">Documentation</a></li>
            </ul>
          </div>
        </div>
    </div>

    <div class="container-fluid">
      
      <div class="row">
        <div class="col-lg-2 sidebar">
            <h6 class="sub-header">Indexes</h6>
            <?php if ($indices): ?>
              <?php foreach ($indices as $in): ?>
                <div class="panel-group" id="accordion">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$in->Index; ?>">
                          <?=$in->Index; ?> (<?=$in->Type?>)
                        </a>
                      </h4>
                    </div>
                    <div id="collapse<?=$in->Index; ?>" class="panel-collapse collapse">
                      <div class="panel-body">
                        <table>
                          <?php foreach (DB::query("DESCRIBE {$in->Index}") as $f): ?>
                            <tr>
                              <td><small><code><?=$f->Field?>: <?=$f->Type?></code></small></td>
                            </tr>
                          <?php endforeach; ?>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="col-lg-10 main">
          <div class="row">
            <div class="col-lg-12 main">
              <h6 class="sub-header">Query</h6>
              <form method="post" action="#" id="request">
                <div class="col-lg-10">
                    <input name="query" type="text" value="" class="form-control">
                </div>
                <div class="col-lg-2">
                    <a href="#" class="btn btn-block btn-default btn-success request" onclick="dbRequest();">Go</a>
                </div>
              </form>  
            </div>
          </div>

            <h6 class="sub-header">Rows</h6>
            <div class="tile">
              <?php if (!empty($data) and !empty($meta)): ?>
                <small>
                  Total rows returned: <?=count($data);?><br/>
                   Total rows found: <?=$metadata->total_found;?><br/>
                  Time taken: <?=$metadata->time;?> 
                </small>
              <?php else: ?>
                <small>Nothing found or there was an error in the query</small>
              <?php endif; ?>
            </div>

            <div class="table-responsive">
            <?php if (isset($data) and $data): ?>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <?php $fields = reset($data); ?>
                    <?php foreach ($fields as $n => $f): ?>
                      <th><?=$n?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php $c = 1; foreach ($data as $value): ?>
                     <tr>
                      <td><?=$c++?></td>
                      <?php foreach ($value as $k => $v): ?>
                        <td><?=$v?></td>
                      <?php endforeach; ?>
                    </tr> 
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 text-center">
        <span>
          Licensed under <a href="http://www.dbad-license.org/" target="_blank">DBAD</a> license! 
        </span>
      </div> 
    </div>

    <!-- Bootstrap -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://raw.githubusercontent.com/designmodo/Flat-UI/master/css/flat-ui.css">
    <script type="text/javascript">
      function dbRequest() {
        document.getElementById("request").submit();
      }
    </script>
  </body>
  </html>