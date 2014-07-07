<?php

  // Connection settings
  $host = '127.0.0.1';
  $port = 9306;
  
  /**
   * Bootstrapping and quering
   */
  new DB($host, $port);

  if (isset($_POST['query']) and $_POST['query'])
    $data = DB::query($_POST['query']);

  $indices = DB::query('SHOW TABLES');

  /**
   * Query class
   */
  class DB
  {
    public static $connection;

    public function __construct($host = '127.0.0.1', $port = 9306)
    {
      self::$connection = new \MySQLi($host, null, null, null, $port, null);
    }

    public static function query($q, $debug = false)
    {
      $data = [];

      if ( ! $result = self::$connection->query($q))
        return false;
      
      while ($row = $result->fetch_assoc()) 
        $data[] = (object)$row;

      return $data;
    }

    /**
     * Describe index
     * @param  string $index 
     * @return 
     */
    public static function getFields($index)
    {
      return DB::query("DESCRIBE $index");
    }

  }

?>
<!-- Template view -->
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sphinx Viewer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
      
      body {
        padding-top: 50px;
      }

    </style>
  </head>
  <body>

  <!-- Navigation bar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Sphinx viewer</a>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a href="http://sphinxsearch.com/docs/">Documentation</a></li>
              <li><a href="#">Help</a></li>
            </ul>

          </div>
        </div>
    </div>

    <div class="container-fluid">
      
      <div class="row">

        <div class="col-lg-2 sidebar">
            <h6 class="sub-header">Indexes</h6>
            
            <!-- Indexes list -->
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
                          <?php foreach (DB::getFields($in->Index) as $f): ?>
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
            
            <!-- Query form -->
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
              Total rows found: <?php echo (isset($data) and $data) ? count($data) : 0;?>
            </div>

            <!-- Results -->
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

    <!-- Bootstrap core JavaScript -->
    <!-- ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- Latest compiled and minified CSS -->
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