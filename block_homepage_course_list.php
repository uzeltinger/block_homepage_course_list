<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course list block.
 *
 * @package    block_homepage_course_list
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/config.php');
include_once($CFG->dirroot . '/course/lib.php');

class block_homepage_course_list extends block_base {
    function init() {
        $this->title = '';//get_string('pluginname', 'block_homepage_course_list');
    }

    function has_config() {
        return true;
    }

    /**
     * Set the instance title and merge instance config as soon as nstance data is loaded
     */
    public function specialization() {

        if (isset($this->config->title) && $this->config->title != '') {
            //$this->title = format_string($this->config->title, true, ['context' => $this->context]);
        }

        if (isset($this->config->filters) && $this->config->filters != '') {
            $this->fclconfig->filters = $this->config->filters;
        }
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $this->content->text = '';

        if(!empty($this->config->title)){$this->content->title = $this->config->title;} else {$this->content->title = '';}
        $this->content->enrol_btn_text = "Ingresar al aula";

$courses = get_courses();
unset($courses[1]);
//echo '<pre>';print_r($courses);
        $chelper = new coursecat_helper();

$this->content->text .= '<style>

.block_homepage_course_list {
    border: none;
}

.block_homepage_course_list {
}

.block_homepage_course_list .card-title {
    text-align: center;
    width: 100%;
    display: block!important;
}
.block_homepage_course_list .details {
    padding: 10px;
    background: #FBFBFB;
}
.block_homepage_course_list .details h5 {
    font-size: 16px;
    line-height: 20px;
    color: #000000;
    font-weight: 700;
}
.block_homepage_course_list .course_detail {
    height: 100%;
    border-radius: 0px 0px 3px 3px;
    border-style: solid;
    border-width: 0px 1px 1px 1px;
    border-color: #E7E7E7;
}

.block_homepage_course_list .footer {
    padding: 10px;
}

.block_homepage_course_list .elementor-button {
    display: inline-block;
    width: 100%;
    line-height: 1;
    background-color: #818a91;
    font-size: 15px;
    padding: 12px 24px;
    border-radius: 3px;
    color: #fff;
    fill: #fff;
    text-align: center;
    -webkit-transition: all .3s;
    -o-transition: all .3s;
    transition: all .3s;
    box-shadow: none;
    text-decoration: none;
    color: #FFFFFF;
    background-color: #1290CB;
    padding: 15px 40px 15px 40px;
}



.block_homepage_course_list .content_ {
    display: flex;
    justify-content: space-between;
    flex-direction: column;
  }
  
.block_homepage_course_list .content_ .upper_ {
    justify-content: normal;
  }
  
        </style>';









        $this->content->text .= '
        <section id="our-top-courses" class="block_homepage_course_list">
          <div class="container">
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
                <div class="main-title text-center">';
                if(!empty($this->content->title)){
                  $this->content->text .='<h3 class="mt0" data-ccn="title">'. format_text($this->content->title, FORMAT_HTML, array('filter' => true)) .'</h3>';
                }
                if(!empty($this->content->subtitle)){
                  $this->content->text .=' <p data-ccn="subtitle">'.format_text($this->content->subtitle, FORMAT_HTML, array('filter' => true)).'</p>';
                }
                $this->content->text .='
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">';



              $this->content->text .='
                  <div class="row" id="mmmmm">';

              $total_courses = count($courses);

              if($total_courses < 2) {
                $col_class = 'col-md-12';
              } else if($total_courses == 2) {
                $col_class = 'col-md-6';
              } else if($total_courses == 3) {
                $col_class = 'col-md-4';
              } else  {
                $col_class = 'col-md-12 col-lg-6 col-xl-4 mb-4';
              }

              $col_class = 'col-md-12 col-lg-6 col-xl-4 mb-4';


                if(!isset($courses) || (count($courses) == 0)) {
                    $this->content->text .= '<div class="col-12"> ';
                    $this->content->text .=    '<h2 class="text-center">No se encontraron cursos</h2> ';
                    if($cat>0){
                        $this->content->text .= '<h3 class="text-center">para la categoría: '.$categories[$cat]['title'].'</h3>';
                    }
                    if($title!=''){
                        $this->content->text .= '<h3 class="text-center">para el texto: '.$title.'</h3>';
                    }

                    $this->content->text .= '</div>' ;
                }

              foreach ($courses as $course) {

                      $course = new core_course_list_element($course);
                      $coursename = $chelper->get_course_formatted_name($course);
                      $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                                                                $coursename, array('class' => $course->visible ? '' : 'dimmed'));
                      $enrolmentLink = $CFG->wwwroot . '/enrol/index.php?id=' . $course->id;
                      $courseUrl = new moodle_url('/course/view.php', array('id' => $course->id));

                    $contentimages = $contentfiles = '';
                    foreach ($course->get_course_overviewfiles() as $file) {
                        $isimage = $file->is_valid_image();
                        $url = file_encode_url("{$CFG->wwwroot}/pluginfile.php",
                                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                        if ($isimage) {
                            $contentimages .= html_writer::empty_tag('img', array('src' => $url, 'alt' => $coursename, 'class' => 'img-fluid'));
                            //$contentimages .='<img class="img-whp" src="'. $url .'" alt="'.$coursename.'">';
                        } else {
                           /* $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                            $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                                    html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                            $contentfiles .= html_writer::tag('span',
                                    html_writer::link($url, $filename),
                                    array('class' => 'coursefile fp-filename-icon'));*/
                        }
                    }



              $this->content->text .='

              <div class="'.$col_class.' cat-'.$course->category.'">
                <div class="course_detail content_">';
                    $this->content->text .='
                    <div class="thumb cursor-pointer upper"> 
                    '.$contentimages. $contentfiles.'                    
                    </div>';

                $this->content->text .='
                  <div class="details upper_">
                    <div class="tc_content">';
                    $this->content->text .=  '<a href="'. $courseUrl .'"><h5>'. $coursename .'</h5></a>';
                    $this->content->text .='
                    </div>
                    </div>';
                    
                    $this->content->text .='
                    <div class="footer">';
                       $this->content->text .='<a href="'.$courseUrl.'" class="elementor-button" data-ccn="enrol_btn_text">'.format_text($this->content->enrol_btn_text, FORMAT_HTML, array('filter' => true)).'</a>';
                  
                      $this->content->text .='
                    </div>';
                    
                  $this->content->text .='
                </div>
              </div>';


}

    $this->content->text .='</div>';
    $this->content->text .='</div></div>';


//echo $this->content->button_text;  echo $this->content->button_link; die();

if(!empty($this->content->button_text) && !empty($this->content->button_link)){
    $this->content->text .='
    <div class="row">
    <div class="col-lg-6 offset-lg-3">
      <div class="courses_all_btn text-center">
        <a class="btn btn-transparent" data-ccn="button_text" href="'.format_text($this->content->button_link, FORMAT_HTML, array('filter' => true)).'">'.format_text($this->content->button_text, FORMAT_HTML, array('filter' => true)).'</a>
      </div>
    </div></div>'  ;
    }
$this->content->text .='</div></section>';


















        return $this->content;
    }

}


