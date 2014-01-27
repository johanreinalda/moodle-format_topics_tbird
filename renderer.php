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
 * Renderer for outputting the topics_tbird course format.
 *
 * @package format_topics_tbird
 * @copyright 2012 Dan Poltawski
 * @copyright 2013 onwards Johan Reinalda {http://www.reinalda.net}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');

/**
 * Basic renderer for topics_tbird format.
 *
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_topics_tbird_renderer extends format_section_renderer_base {

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);

        // Since format_topics_renderer::section_edit_controls() only displays the 'Set current section' control when editing mode is on
        // we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any other managing capability.
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'topics'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('topicoutline');
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_controls($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $controls = array();
        if (has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $controls[] = html_writer::link($url,
                                    html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marked'),
                                        'class' => 'icon ', 'alt' => get_string('markedthistopic'))),
                                    array('title' => get_string('markedthistopic'), 'class' => 'editing_highlight'));
            } else {
                $url->param('marker', $section->section);
                $controls[] = html_writer::link($url,
                                html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/marker'),
                                    'class' => 'icon', 'alt' => get_string('markthistopic'))),
                                array('title' => get_string('markthistopic'), 'class' => 'editing_highlight'));
            }
        }

        return array_merge($controls, parent::section_edit_controls($course, $section, $onsectionpage));
    }
    

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * This section_header() is copied the base class plugin_renderer_base(),
     * defined in /course/format/renderer.php, and modified for section 0 - JKR
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {
    	global $PAGE;
    
    	$o = '';
    	$currenttext = '';
    	$sectionstyle = '';
    
    	if ($section->section != 0) {
    		// Only in the non-general sections.
    		if (!$section->visible) {
    			$sectionstyle = ' hidden';
    		} else if (course_get_format($course)->is_section_current($section)) {
    			$sectionstyle = ' current';
    		}
    	}
    
    	$o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
    			'class' => 'section main clearfix'.$sectionstyle));
    
    	$leftcontent = $this->section_left_content($section, $course, $onsectionpage);
    	$o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));
    
    	$rightcontent = $this->section_right_content($section, $course, $onsectionpage);
    	$o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
    	$o.= html_writer::start_tag('div', array('class' => 'content'));
    
    	// When not on a section page, we display the section titles except the general section if null
    	$hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));
    
    	// When on a section page, we only display the general section title, if title is not the default one
    	$hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));
    
    	//overview section 0 title is always course name!
    	if($section->section == 0) {
    		$o.= $this->output->heading($course->fullname, 3, 'sectionname');
    	} else if ($hasnamenotsecpg || $hasnamesecpg) {
   			$o.= $this->output->heading($this->section_title($section, $course), 3, 'sectionname');
    	}
    
    	$o.= html_writer::start_tag('div', array('class' => 'summary'));
    	
	    //get regular editable section summary text
    	$sectionsummary = $this->format_summary_text($section);
    	
    	//if section 0, show the Course Summary field above the editable content.
    	if($section->section == 0) {
    		//$o .= '<h3><center>' . $course->fullname . '</center></h3>';
    		$o .= '<p>' . $course->summary . '<p>';
    		//only add HR separator if editable section summary set
    		//if($sectionsummary !== '')
    			//always add it
    			$o .= '<hr/>';
    		
    	}
    	$o .= $sectionsummary;
    
    	$context = context_course::instance($course->id);
    	if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
    		$url = new moodle_url('/course/editsection.php', array('id'=>$section->id, 'sr'=>$sectionreturn));
    		$o.= html_writer::link($url,
    				html_writer::empty_tag('img', array('src' => $this->output->pix_url('t/edit'),
    						'class' => 'iconsmall edit', 'alt' => get_string('edit'))),
    				array('title' => get_string('editsummary')));
    	}
    	$o.= html_writer::end_tag('div');
    
    	$o .= $this->section_availability_message($section,
    			has_capability('moodle/course:viewhiddensections', $context));
    
    	return $o;
    }
    
    /**
     * Renders course header/footer
     *
     * @param renderable $obj
     * @return string
     */
    public function render_format_topics_tbird_courseobj($obj) {
    	return html_writer::tag('div', "<b>{$obj->text}</b>",
    	array('style' => 'background: #'.$obj->background.'; border: 1px solid black; text-align: center; padding: 5px;'));
    }
    
    
}
